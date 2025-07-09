<?php
namespace app\models;
use Illuminate\Database\Eloquent\Model;
class Feedback extends Model
{
    protected $table = 'feedback';
    public $timestamps = false;
    protected $fillable = ['nome', 'email', 'feedback'];
    public ?string $error = null;
    public function validate(): bool
    {
        if (strpos($this->email, '@') !== false) {
            return true;
        }

        $this->error = 'Email deve conter @';
        return false;
    }

    public function save(array $options = []): bool
    {
        if ($this->validate()) {
            // Chama o método original save() da classe pai (Eloquent\Model).
            return parent::save($options);
        }

        return false;
    }

}