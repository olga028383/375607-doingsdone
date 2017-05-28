<?php
/**
 * class AuthForm
 */
class AuthForm extends BaseForm {
    public $formName = 'auth';

    protected $fields = ['email', 'password'];

    protected $rules = [
        ['email', ['email']],
        ['required', ['email', 'password']]
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
            if (!filter_var($field, FILTER_VALIDATE_EMAIL)) {
                $result = false;
                $this->errors[$value] = 'Введите корректный e-mail';
            }
        }
        return $result;
    }

    public function addBadEmailOrPasswordError() {
        $this->errors['email'] = 'Пользователя с таким именем  паролем не существует, воспользуйтесь формой регистрации';
    }

}