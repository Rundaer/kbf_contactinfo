<?php
/**
* 2007-2020 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2020 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

use PrestaShop\PrestaShop\Core\Module\WidgetInterface;

class Kb_footercontactinfo extends Module implements WidgetInterface
{
    private $templates = array (
        'default' => 'kbf_contactinfo.tpl',
    );

    public function __construct()
    {
        $this->name = 'kb_footercontactinfo';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'Konrad Babiarz';
        $this->need_instance = 0;

        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Footer belt contact info');
        $this->description = $this->l('Module to display contact info in footer');

        $this->ps_versions_compliancy = array('min' => '1.7', 'max' => _PS_VERSION_);
    }

    /**
     * Don't forget to create update methods if needed:
     * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
     */
    public function install()
    {
        return parent::install() &&
            $this->registerHook('displayFooterBefore');
    }

    public function uninstall()
    {
        return parent::uninstall();
    }

    /**
     * Load the configuration form
     */
    public function getContent()
    {
        $output = null;
        /**
         * If values have been submitted in the form, process.
         */
        if (((bool)Tools::isSubmit('submitKb_footercontactinfoModule')) == true) {
            $this->postProcess();
            $output = $this->displayConfirmation($this->l('Settings updated'));
        }

        return $output.$this->renderForm();
    }

    /**
     * Create the form that will be displayed in the configuration of your module.
     */
    protected function renderForm()
    {
        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitKb_footercontactinfoModule';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            .'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFormValues(), /* Add values for your inputs */
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        return $helper->generateForm(array($this->getConfigForm()));
    }

    /**
     * Create the structure of your form.
     */
    protected function getConfigForm()
    {
        return array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Settings'),
                    'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Show Number'),
                        'name' => 'KBF_PHONE',
                        'is_bool' => true,
                        'desc' => $this->l('Show number in footer contact if number exists'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Show Email'),
                        'name' => 'KBF_EMAIL',
                        'is_bool' => true,
                        'desc' => $this->l('Show email in footer contact'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Shows text'),
                        'name' => 'KBF_TEXT',
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ),
        );
    }

    /**
     * Set values for the inputs.
     */
    protected function getConfigFormValues()
    {
        return array(
            'KBF_PHONE' => Configuration::get('KBF_PHONE', true),
            'KBF_EMAIL' => Configuration::get('KBF_EMAIL', true),
            'KBF_TEXT' => Configuration::get('KBF_TEXT', null),
        );
    }

    /**
     * Save form data.
     */
    protected function postProcess()
    {
        $form_values = $this->getConfigFormValues();

        foreach (array_keys($form_values) as $key) {
            Configuration::updateValue($key, Tools::getValue($key));
        }
    }

    /**
     * Get variables to rendered widget
     */
    public function getWidgetVariables($hookName = null, array $configuration = [])
    {
        $contact_data = [
            'isEmail' => Configuration::get('KBF_EMAIL'),
            'isPhone' => Configuration::get('KBF_PHONE'),
            'phone' => Configuration::get('PS_SHOP_PHONE'),
            'email' => Configuration::get('PS_SHOP_EMAIL'),
            'text' => Configuration::get('KBF_TEXT')
        ];

        return [
            'contact_data' => $contact_data,
        ];
    }

    /**
     * Renders Widget
     */
    public function renderWidget($hookName = null, array $configuration = [])
    {
        if ($hookName == null && isset($configuration['hook'])) {
            $hookName = $configuration['hook'];
        }

        // Limited to hooks
        // if ($hookName == 'displayLeftColumn') {
        //     $template_file = $this->templates['light'];
        // } 
        
        //Default
        $template_file = $this->templates['default'];
        $this->context->controller->addCSS($this->_path.'views/css/default.css');
        
        $this->smarty->assign($this->getWidgetVariables($hookName, $configuration));

        return $this->fetch('module:'.$this->name.'/views/templates/hook/'.$template_file);
    }
}
