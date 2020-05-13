<?php
//bot_rdmm_client
namespace Drupal\bot_rdmm_client\Controller;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\HttpFoundation\Response;
use Drupal\Core\Extension\ModuleHandler;
use Drupal\Core\Extension\ExtensionDiscovery;
use Drupal\Component\Utility;

define('BOT_RDMM_CLIENT_ENDPOINT', 'https://backofficethinking.com/bot-rdmm');

class BotRdmmController extends ConfigFormBase {

	public function getFormId() {
    return 'bot_rdmm_contact_setting';
  }

  protected function getEditableConfigNames() {
    return [
      'rdmm.setting',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('rdmm.setting');

    $form['bot_rdmm_client_contact_id'] = array(
      '#type' => 'textfield',
	    '#title' => t('Contact ID'),
	    '#maxlength' => 255,
	    '#required' => TRUE,
      '#default_value' => $config->get('bot_rdmm_client_contact_id'),
    );

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
      // Retrieve the configuration
       $this->configFactory->getEditable('rdmm.setting')
      // Set the submitted configuration setting
      ->set('bot_rdmm_client_contact_id', $form_state->getValue('bot_rdmm_client_contact_id'))
      ->save();

    parent::submitForm($form, $form_state);
    \Drupal::state()->set('bot_rdmm_client_contact_id', $form_state->getValue('bot_rdmm_client_contact_id'));
  }

  public function bot_rdmm_client_test() {
  	return $this->_bot_rdmm_client_send_modules_statuses();
  }

  public function _bot_rdmm_client_send_modules_statuses(){

  	$contact_id = \Drupal::state()->get('bot_rdmm_client_contact_id');

    if (!$contact_id) {
    	return array(
	      '#type' => 'markup',
	      'value' => t('Contact ID not configured!')
	    );
    }

    $return = array(
      'contact_id' => $contact_id,
      'client_id' =>  '0aa1883c6411f7873cb83dacb17b0afc',
      'modules' =>    array(),
    );

    //Get all modules present in system
    $modules = system_get_info('module', $name = NULL);

		//Get module update information
		module_load_include('module', 'update');
		module_load_include('inc', 'update', 'update.report');
		$available = update_get_available(TRUE);
		$data = update_calculate_project_data($available);

		if($modules) foreach($modules as $key => $module) {

			if($module['package'] == 'Core'){
				continue;
			}

		  $module_info = array();
		  $module_info['version_installed'] = $module_info['version_current'] = false;

		  $module_info['name'] = $key;
	    $module_info['package'] = $module['package'];
	    $module_info['name_display'] = $module['name'];
	    $module_info['version_installed'] = $module['version'];


		  //Get update information if available
		  if (isset($data[$module_info['name']])) {
		    $module_info['version_current'] = $data[$module_info['name']]['latest_version'];
		  }

		  //Is it a security release?
		  if (isset($data[$module_info['name']]['security updates'])) {
		    $module_info['security_release'] = true;
		  } else {
		    $module_info['security_release'] = false;
		  }

		  $module_info['up_to_date'] = $module_info['version_installed'] === $module_info['version_current'];

		  $moduleHandler = \Drupal::service('module_handler');
	    if ($moduleHandler->moduleExists($module_info['name'])){
	       $module_info['enabled'] = true;
	    }

		  $return['modules'][] = $module_info;
		}

		$return['version_drupal'] = \DRUPAL::VERSION;

		$return = json_encode($return);

		$data = array(
			'body' => $return,
	  );

	  $success = FALSE;
	  $curl = curl_init();
	  curl_setopt_array($curl, array(
	    CURLOPT_URL => BOT_RDMM_CLIENT_ENDPOINT,
	    CURLOPT_RETURNTRANSFER => true,
	    CURLOPT_ENCODING => "",
	    CURLOPT_MAXREDIRS => 10,
	    CURLOPT_TIMEOUT => 30,
	    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	    CURLOPT_CUSTOMREQUEST => "POST",
	    CURLOPT_POSTFIELDS => \Drupal\Component\Utility\UrlHelper::buildQuery($data),
	    CURLOPT_HTTPHEADER => array(
	      "cache-control: no-cache",
	      "content-type: application/x-www-form-urlencoded",
	    ),
	  ));

	  $response = curl_exec($curl);
	  $err = curl_error($curl);

	  curl_close($curl);

	  if ($err) {
	    $success = FALSE;
	    $status = 'Failure';
      return array(
    	  '#markup' => t('Call failed! There was a problem in sending info'),
    	);
	  } else {
	    $success = TRUE;
	    $status = 'Success';
	  }

	  \Drupal::logger('bot_rdmm')->notice('BOT RDMM Client module status transmissionss: @status', array('@status' => $status));

    if($success){
    	return array(
    	  '#markup' => t('Info sent'),
    	);
    }
    else{
    	return array(
    	  '#markup' => t('There was a problem in sending info'),
    	);
    }
  }
}