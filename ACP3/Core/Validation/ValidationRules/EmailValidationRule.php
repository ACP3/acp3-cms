<?php
namespace ACP3\Core\Validation\ValidationRules;

class EmailValidationRule extends AbstractValidationRule
{
    /**
     * @inheritdoc
     */
    public function isValid($data, $field = '', array $extra = [])
    {
        if (\is_array($data) && \array_key_exists($field, $data)) {
            return $this->isValid($data[$field], $field, $extra);
        }

        return (bool)\filter_var($data, FILTER_VALIDATE_EMAIL);
    }
}
