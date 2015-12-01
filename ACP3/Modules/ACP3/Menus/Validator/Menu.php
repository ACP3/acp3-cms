<?php
namespace ACP3\Modules\ACP3\Menus\Validator;

use ACP3\Core;
use ACP3\Modules\ACP3\Menus\Validator\ValidationRules\MenuExistsValidationRule;
use ACP3\Modules\ACP3\Menus\Validator\ValidationRules\MenuNameValidationRule;

/**
 * Class Menu
 * @package ACP3\Modules\ACP3\Menus\Validator
 */
class Menu extends Core\Validator\AbstractValidator
{
    /**
     * @param array $formData
     * @param int   $menuId
     *
     * @throws Core\Exceptions\InvalidFormToken
     * @throws Core\Exceptions\ValidationFailed
     */
    public function validate(array $formData, $menuId = 0)
    {
        $this->validator
            ->addConstraint(Core\Validator\ValidationRules\FormTokenValidationRule::NAME)
            ->addConstraint(
                MenuNameValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'index_name',
                    'message' => $this->lang->t('menus', 'type_in_index_name')
                ])
            ->addConstraint(
                MenuExistsValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'index_name',
                    'message' => $this->lang->t('menus', 'index_name_unique'),
                    'extra' => [
                        'menu_id' => $menuId
                    ]
                ])
            ->addConstraint(
                Core\Validator\ValidationRules\NotEmptyValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'title',
                    'message' => $this->lang->t('menus', 'menu_bar_title_to_short')
                ]);

        $this->validator->validate();
    }

}
