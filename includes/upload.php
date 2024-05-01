<?php

    class  Media
    {

        public $imageInfo;
        public $fileName;
        public $fileType;
        public $fileTempPath;
        //Set destination for upload
        public $userPath = SITE_ROOT . DS . '..' . DS . 'uploads/users';
        public $productPath = SITE_ROOT . DS . '..' . DS . 'uploads/products';


        public $errors = array();
        public $upload_errors = array(
            0 => 'Ошибки нет, файл успешно загружен',
            1 => 'Загруженный файл превышает директиву upload_max_filesize в php.ini',
            2 => 'Загруженный файл превышает директиву MAX_FILE_SIZE, которая была указана в HTML-форме',
            3 => 'Загруженный файл был загружен только частично',
            4 => 'Ни один файл не был загружен',
            6 => 'Отсутствует временная папка',
            7 => 'Не удалось записать файл на диск.',
            8 => 'Расширение PHP остановило загрузку файла.'
        );
        public $upload_extensions = array(
            'gif',
            'jpg',
            'jpeg',
            'png',
        );

        public function file_ext($filename)
        {
            $ext = strtolower(substr($filename, strrpos($filename, '.') + 1));
            if (in_array($ext, $this->upload_extensions)) {
                return true;
            }
        }

        public function upload($file)
        {
            if (!$file || empty($file) || !is_array($file)):
                $this->errors[] = "Ни один файл не был загружен.";
                return false;
            elseif ($file['error'] != 0):
                $this->errors[] = $this->upload_errors[$file['error']];
                return false;
            elseif (!$this->file_ext($file['name'])):
                $this->errors[] = 'Файл неправильного формата ';
                return false;
            else:
                $this->imageInfo = getimagesize($file['tmp_name']);
                $this->fileName = basename($file['name']);
                $this->fileType = $this->imageInfo['mime'];
                $this->fileTempPath = $file['tmp_name'];
                return true;
            endif;

        }

        public function process()
        {

            if (!empty($this->errors)):
                return false;
            elseif (empty($this->fileName) || empty($this->fileTempPath)):
                $this->errors[] = "Местоположение файла было недоступно.";
                return false;
            elseif (!is_writable($this->productPath)):
                $this->errors[] = $this->productPath . " Должно быть доступно для записи!!!.";
                return false;
            elseif (file_exists($this->productPath . "/" . $this->fileName)):
                $this->errors[] = "Файл {$this->fileName} уже существует.";
                return false;
            else:
                return true;
            endif;
        }
        /*--------------------------------------------------------------*/
        /* Функция для обработки медиафайла
        /*--------------------------------------------------------------*/
        public function process_media()
        {
            if (!empty($this->errors)) {
                return false;
            }
            if (empty($this->fileName) || empty($this->fileTempPath)) {
                $this->errors[] = "Местоположение файла было недоступно.";
                return false;
            }

            if (!is_writable($this->productPath)) {
                $this->errors[] = $this->productPath . " Должно быть доступно для записи!!!.";
                return false;
            }

            if (file_exists($this->productPath . "/" . $this->fileName)) {
                $this->errors[] = "Файл {$this->fileName} уже существует.";
                return false;
            }

            if (move_uploaded_file($this->fileTempPath, $this->productPath . '/' . $this->fileName)) {

                if ($this->insert_media()) {
                    unset($this->fileTempPath);
                    return true;
                }

            } else {

                $this->errors[] = "Не удалось загрузить файл, возможно, из-за неправильных прав доступа к папке загрузки.";
                return false;
            }

        }
        /*--------------------------------------------------------------*/
        /* Функция для обработки изображения пользователя
        /*--------------------------------------------------------------*/
        public function process_user($id)
        {

            if (!empty($this->errors)) {
                return false;
            }
            if (empty($this->fileName) || empty($this->fileTempPath)) {
                $this->errors[] = "Местоположение файла было недоступно.";
                return false;
            }
            if (!is_writable($this->userPath)) {
                $this->errors[] = $this->userPath . " Должно быть доступно для записи!!!.";
                return false;
            }
            if (!$id) {
                $this->errors[] = " Отсутствует идентификатор пользователя.";
                return false;
            }
            $ext = explode(".", $this->fileName);
            $new_name = randString(8) . $id . '.' . end($ext);
            $this->fileName = $new_name;
            if ($this->user_image_destroy($id)) {
                if (move_uploaded_file($this->fileTempPath, $this->userPath . '/' . $this->fileName)) {

                    if ($this->update_userImg($id)) {
                        unset($this->fileTempPath);
                        return true;
                    }

                } else {
                    $this->errors[] = "Не удалось загрузить файл, возможно, из-за неправильных прав доступа к папке загрузки.";
                    return false;
                }
            }
        }
        /*--------------------------------------------------------------*/
        /* Функция для обновления изображения пользователя
        /*--------------------------------------------------------------*/
        private function update_userImg($id)
        {
            global $db;
            $sql = "UPDATE users SET";
            $sql .= " image='{$db->escape($this->fileName)}'";
            $sql .= " WHERE id='{$db->escape($id)}'";
            $result = $db->query($sql);
            return ($result && $db->affected_rows() === 1 ? true : false);

        }
        /*--------------------------------------------------------------*/
        /* Функция удаления старого изображения
        /*--------------------------------------------------------------*/
        public function user_image_destroy($id)
        {
            $image = find_by_id('users', $id);
            if ($image['image'] === 'no_image.png') {
                return true;
            } else {
                unlink($this->userPath . '/' . $image['image']);
                return true;
            }

        }
        /*--------------------------------------------------------------*/
        /* Функция для вставки мультимедийного изображения
        /*--------------------------------------------------------------*/
        private function insert_media()
        {

            global $db;
            $sql = "INSERT INTO media ( file_name,file_type )";
            $sql .= " VALUES ";
            $sql .= "(
                  '{$db->escape($this->fileName)}',
                  '{$db->escape($this->fileType)}'
                  )";
            return ($db->query($sql) ? true : false);

        }
        /*--------------------------------------------------------------*/
        /* Функция удаления носителя по идентификатору
        /*--------------------------------------------------------------*/
        public function media_destroy($id, $file_name)
        {
            $this->fileName = $file_name;
            if (empty($this->fileName)) {
                $this->errors[] = "Имя файла с фотографией отсутствует.";
                return false;
            }
            if (!$id) {
                $this->errors[] = "Пропало ID с фотографией.";
                return false;
            }
            if (delete_by_id('media', $id)) {
                unlink($this->productPath . '/' . $this->fileName);
                return true;
            } else {
                $this->error[] = "Не удалось удалить фотографию Или отсутствует Prm.";
                return false;
            }

        }


    }

