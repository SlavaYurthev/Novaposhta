<?php
/**
 * Nova Poshta API 2.0
 * 
 * @author Slava Yurthev
 */
class SY_Novaposhta_Block_Adminhtml_System_Config_Developer_Renderer extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
    	$html = '<span style="font-weight:bold;text-transform:uppercase;">'.$element->getValue()."</span><br><br>";
    	$html .= '<table>';
    	$html .= '<tr><td style="padding-right:20px;">Telegram:</td><td><a href="https://telegram.me/darks_virus" target="_blank">darks_virus</a></td></tr>';
    	$html .= '<tr><td>Skype:</td><td><a href="skype:darks_v1rus?chat" target="_blank">darks_v1rus</a></td></tr>';
    	$html .= '<tr><td>Email:</td><td><a href="mailto:slavik-iii@ukr.net" target="_blank">slavik-iii@ukr.net</a></td></tr>';
    	$html .= '</table>';
    	$html .= '<br><br>По всем вопросам лучше всего писать в телеграмм ;)<br><br>';
		$html .= '<img src="http://'.$_SERVER['SERVER_NAME'].'/media/novaposhta/logo.png" />';
        return $html;
    }
}