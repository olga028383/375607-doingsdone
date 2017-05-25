<?php
/**
 * class SignupForm
*/
class SignupForm extends BaseForm {

    public $formName = 'signupForm';

    protected $fields = ['email', 'password', 'name'];

    protected $rules = [
        ['email', ['email']],
        ['required', ['email', 'password', 'name']]
    ];
    /**
     * Проверят e-mail на корректность заполнения
     * @param array $fields Список полей для проверки
     * @return bool результат проверки
    */
    protected function runEmailValidator($fields)
    {
        $result = true;
        foreach($fields as $value){
            $field = $this->formData[$value];
            if(!filter_var($field, FILTER_VALIDATE_EMAIL)){
                $result = false;

                $this->errors[$value] = 'Введите корректный e mail';
            }
        }

        return $result;
    }
    /**
     * Проверяет, что загруженный файл является изображением
     * @param string $field  поле с изображением
     * @param string $allowed_mime Допустимый mime_type
     * @return bool  результат проверки
    */
    protected function runImageValidator($field, $allowed_mime = '')
    {
        $result = true;
        if(isset($_POST[$this->formName])){
            $allowed_mime = ['image/jpeg', 'image/img', 'image/gif', 'image/tiff'];
            if($allowed_mime){
                $allowed_types = [$allowed_mime];
            }
            $file = $_FILES[$this->formName]['tmp_name'][$field];
            if($file){
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mime = finfo_file($finfo, $file);

                $result = in_array($mime, $allowed_types );
            }
            if(!$result){
                $this->errors[$field] = 'Загруженный файл должен быть изображение';
            }
            return $result;
        }
    }
}