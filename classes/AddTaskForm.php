<?php

/**
 * class AddTaskForm
 */
class AddTaskForm extends BaseForm
{

    public $formName = 'task';

    protected $fields = ['task', 'project', 'deadline'];

    protected $rules = [
        ['deadline', ['deadline']],
        ['required', ['task', 'project', 'deadline']]
    ];

    /**
     * Проверят время на корректность заполнения
     * @param array $fields Список полей для проверки
     * @return bool результат проверки
     */
    function runDeadlineValidator($fields)
    {
        $result = true;
        foreach ($fields as $value) {
            $field = $this->formData[$value];
            if (!checkForDateCorrected($field)) {
                $result = false;
                $this->errors[$value] = 'Дата введена некорректно';
            }
        }

        return $result;
    }
}