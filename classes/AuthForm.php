<?php
/**
 * class AuthForm
 */
class AuthForm extends BaseForm {
    public $formName = 'auth';

    public $user;

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
        $user = User::getUser($this->formData['email']);
        foreach($fields as $value){
            $field = $this->formData[$value];
            if(filter_var($field, FILTER_VALIDATE_EMAIL) && !$user){
                $result = false;
                $this->errors[$value] = 'Пользователя с таким именем не существует, воспользуйтесь формой регистрации';
            }else{
                $this -> user = $user;
            }
            if(!filter_var($field, FILTER_VALIDATE_EMAIL)){
                $result = false;
                $this->errors[$value] = 'Введите корректный e-mail';
            }

        }

        return $result;
    }

}