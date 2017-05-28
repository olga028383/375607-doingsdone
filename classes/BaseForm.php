<?php

/**
 * Базовый класс для работы с формами
 */
class BaseForm
{
    /**
     * @var array $fields Список имен полей формы
     */
    protected $fields = [];
    /**
     * @var array $errors Список полей формы с ошибками
     */
    protected $errors = [];
    /**
     * @var array $rules Список проверок
     */
    protected $rules = [];
    /**
     * @var array $formData Отправленные поля
     */
    protected $formData = [];
    /**
     * @var string $formName Имя формы
     */
    protected $formName;

    /**
     * BaseForm constructor
     * @param array $data Данные формы
     */
    public function __construct($data = [])
    {
        $this->fillFormData($data);
    }

    /**
     * Проверяем что форма была отправлена
     * @return bool
     */
    public function isSubmitted()
    {
        return isset($_POST[$this->formName]);
    }

    /**
     * Проверяем были ли ошибки валидации
     * @return bool
     */
    public function isValid()
    {
        return empty($this->errors);
    }

    /**
     * Возвращает данные отправленные из формы
     * @return array
     */
    public function getformData()
    {
        return $this->formData;
    }

    /**
     * Возвращает данные конкретного поля
     * @param $field  поле, которое требуется вернуть
     * @return string
     */
    public function getDataField($field)
    {
        return $this->formData[$field];
    }

    /**
     * Возвращает тект ошибки
     * @param $field  поле для которого возвращаем текст ошибки
     * @return string
     */
    public function getError($field)
    {
        return $this->errors[$field] ?? null;
    }

    /**
     * Возвращает список ошибок
     * @return array
     */
    public function getAllError()
    {
        return $this->errors;
    }

    /**
     * Выполняет валидацию формы
     * @return void
     */
    public function validate()
    {
        foreach ($this->rules as $rule) {
            list($ruleName, $fields) = $rule;
            $this->runValidator($ruleName, $fields);
        }
    }

    /**
     * Магический метод для получения значения поля по его имени
     * @param string $name Имя поля
     * @return mixed|null
     */
    public function __get($name)
    {
        return $this->formData[$name] ?? null;
    }

    /**
     * Запускает валидатор по его имени
     * @param string @name Имя валидатора
     * @param array $fields Список имен полей для валидации
     */
    protected function runValidator($name, $fields)
    {
        $method_name = 'run' . ucfirst($name) . 'Validator';
        if (method_exists($this, $method_name)) {
            $this->$method_name($fields);
        }
    }

    /**
     * Проверяет поле на его заполненность
     * @param array $fields Поля для проверки
     * @return bool Результат проверки
     */
    protected function runRequiredValidator($fields)
    {
        $result = true;
        foreach ($fields as $key => $value) {
            if (!$this->formData[$value]) {
                $result = false;
                $this->errors[$value] = 'Это поле должно быть заполнено';
            }
        }
        return $result;
    }

    /**
     * Заполняет данные данными из формы
     * @param array $data данные для заполнения
     */
    protected function fillFormData($data = [])
    {
        if (!$this->isSubmitted()) {
            return;
        }
        $fillData = !empty($data) ? $data : $_POST[$this->formName];
        foreach ($this->fields as $field) {
            $this->formData[$field] = array_key_exists($field, $fillData) ? $fillData[$field] : null;
        }
    }
}