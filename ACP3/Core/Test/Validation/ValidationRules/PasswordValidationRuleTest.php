<?php
namespace ACP3\Core\Test\Validation\ValidationRules;

use ACP3\Core\Validation\ValidationRules\PasswordValidationRule;

class PasswordValidationRuleTest extends AbstractValidationRuleTest
{
    protected function setUp()
    {
        $this->validationRule = new PasswordValidationRule();

        parent::setUp();
    }

    /**
     * @return array
     */
    public function validationRuleProvider()
    {
        return [
            'valid-data-array' => [['pw' => 'test1234', 'pw_confirm' => 'test1234'], ['pw', 'pw_confirm'], [], true],
            'invalid-data-array' => [['pw' => 'test1234'], ['pw'], [], false],
            'invalid-data-flat-array' => [['test1234'], [], [], false],
            'invalid-data-string' => ['foobar', '', [], false],
            'invalid-no-data' => [null, null, [], false],
        ];
    }
}
