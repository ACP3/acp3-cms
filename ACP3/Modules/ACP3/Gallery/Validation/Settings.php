<?php
namespace ACP3\Modules\ACP3\Gallery\Validation;

use ACP3\Core;

/**
 * Class Settings
 * @package ACP3\Modules\ACP3\Gallery\Validator
 */
class Settings extends Core\Validation\AbstractFormValidation
{
    /**
     * @var \ACP3\Core\Modules
     */
    protected $modules;

    /**
     * @param \ACP3\Core\I18n\Translator      $lang
     * @param \ACP3\Core\Validation\Validator $validator
     * @param Core\Modules                    $modules
     */
    public function __construct(
        Core\I18n\Translator $lang,
        Core\Validation\Validator $validator,
        Core\Modules $modules
    )
    {
        parent::__construct($lang, $validator);

        $this->modules = $modules;
    }

    /**
     * @param array $formData
     *
     * @throws Core\Exceptions\InvalidFormToken
     * @throws Core\Exceptions\ValidationFailed
     */
    public function validate(array $formData)
    {
        $this->validator
            ->addConstraint(Core\Validation\ValidationRules\FormTokenValidationRule::NAME)
            ->addConstraint(
                Core\Validation\ValidationRules\InArrayValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'dateformat',
                    'message' => $this->lang->t('system', 'select_date_format'),
                    'extra' => [
                        'haystack' => ['long', 'short']
                    ]
                ])
            ->addConstraint(
                Core\Validation\ValidationRules\IntegerValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'sidebar',
                    'message' => $this->lang->t('system', 'select_sidebar_entries')
                ])
            ->addConstraint(
                Core\Validation\ValidationRules\InArrayValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'overlay',
                    'message' => $this->lang->t('gallery', 'select_use_overlay'),
                    'extra' => [
                        'haystack' => [0, 1]
                    ]
                ])
            ->addConstraint(
                Core\Validation\ValidationRules\IntegerValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'thumbwidth',
                    'message' => $this->lang->t('gallery', 'invalid_image_width_entered')
                ])
            ->addConstraint(
                Core\Validation\ValidationRules\IntegerValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'width',
                    'message' => $this->lang->t('gallery', 'invalid_image_width_entered')
                ])
            ->addConstraint(
                Core\Validation\ValidationRules\IntegerValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'thumbheight',
                    'message' => $this->lang->t('gallery', 'invalid_image_height_entered')
                ])
            ->addConstraint(
                Core\Validation\ValidationRules\IntegerValidationRule::NAME,
                [
                    'data' => $formData,
                    'field' => 'height',
                    'message' => $this->lang->t('gallery', 'invalid_image_height_entered')
                ]);

        if ($this->modules->isActive('comments') === true) {
            $this->validator
                ->addConstraint(
                    Core\Validation\ValidationRules\InArrayValidationRule::NAME,
                    [
                        'data' => $formData,
                        'field' => 'comments',
                        'message' => $this->lang->t('gallery', 'select_allow_comments'),
                        'extra' => [
                            'haystack' => [0, 1]
                        ]
                    ]);
        }

        $this->validator->validate();
    }
}