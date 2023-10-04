<?php
/*
 * Plugin Name:  EPFL Diploma Verification
 * Description:  Provides a shortcode to display diploma validation form
 * Version:      1.0.3
 * Author:       Rosa Maggi
 * License: 	 Copyright (c) 2021 Ecole Polytechnique Federale de Lausanne, Switzerland
 * Text Domain:  epfl-diploma-verification
 * Domain Path:  /languages
 */

function epfl_diploma_verification_process_shortcode()
{
	ob_start();
	wp_enqueue_style('epfl_diploma_verification_style', plugin_dir_url(__FILE__) . 'css/styles.css', [], '2.1');

	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		$prenom = $_POST["prenom"] ?? null;
		$nom = $_POST["nom"] ?? null;
		$diplome = $_POST["diplome"] ?? null;

		include('diploma_verification_form.php');
		call_web_service(sanitize_text_field($prenom), sanitize_text_field($nom), sanitize_text_field($diplome));
		return ob_get_clean();
	} else {
		include('diploma_verification_form.php');
		$output = ob_get_contents();
		ob_end_clean();
		return $output;
	}
}

function call_web_service($prenom, $nom, $diplome)
{
	$apiUrl = "https://isa.epfl.ch/services/diplome/" . $diplome . "/validate";
	$formData = array(
		'prenom' => $prenom,
		'nom' => $nom,
		'diplome' => $diplome
	);

	$serializedData = http_build_query($formData);
	$urlWithParams = $apiUrl . '?' . $serializedData;

	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, $urlWithParams);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

	$response = curl_exec($curl);
	$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

	if ($httpCode === 200) {
		$data = json_decode($response, true);
		success($data);
	} else {
		failure();
	}
	curl_close($curl);
}

function success($data)
{
	?>
	<div id="success" class="row">
		<div class="status-success col-lg-12">
			<div class="row">
				<div class="col-lg-12">
					<h3><label><?php _e('authenticationConfirmed','epfl-diploma-verification'); ?></label></h3>
				</div>
			</div>
			<div class="row">
				<div class="col-lg-4"><label><?php _e('graduateName', 'epfl-diploma-verification'); ?></label></div>
				<div id="r-nom" class="col-lg-8"><?php echo $data[0]['fullName'] ?></div>
			</div>
			<div class="row">
				<div class="col-lg-4"><label><?php _e('documentNumber','epfl-diploma-verification'); ?></label></div>
				<div id="r-diplome" class="col-lg-8"><?php echo $data[0]['numero'] ?></div>
			</div>
			<div class="row">
				<div class="col-lg-4"><label><?php _e('documentTitle','epfl-diploma-verification'); ?></label></div>
				<div id="r-titre_EDOC" class="col-lg-8" style="display:  <?php echo ($data[0]['qualification']=='EDOC' ? 'unset' : 'none') ?>">
					<div>Docteur ès Sciences</div>
					<div style="font-size: small"><?php echo $data[0]['title'] ?></div>
				</div>
				<div id="r-titre_PDM" class="col-lg-8" style="display:  <?php echo ($data[0]['qualification']=='EDOC' ? 'none' : 'unset') ?>"><?php echo $data[0]['title'] ?></div>
			</div>
		</div>
	</div>
	<?php
}

function failure()
{
	?>
	<div id="failure" class="row">
		<div class="status-failure col-lg-12">
			<h3><?php _e('error','epfl-diploma-verification'); ?></h3>
			<div class="row">
				<div class="col-lg-12"><?php _e('errorMessage','epfl-diploma-verification'); ?></div>
			</div>
		</div>
	</div>
	<?php
}

add_action('init', function () {
	add_shortcode('epfl_diploma_verification', 'epfl_diploma_verification_process_shortcode');
	load_plugin_textdomain( 'epfl-diploma-verification', false, 'epfl-diploma-verification/languages/' );
});
