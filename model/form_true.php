<?php /** @noinspection PhpUndefinedMethodInspection */

class User extends ActiveRecord
{
    public $last_name;
    public $first_name;
    public $phone;

    public function rules()
    {
        return [
            [['first_name', 'last_name', 'phone'], 'required'],
        ];
    }
}

class ChangePhoneForm extends Model
{
    public $phone;

    public function rules()
    {
        return [
            [['phone'], 'required'],
        ];
    }
}
