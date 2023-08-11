<?php
/*
Plugin Name:  EPFL Diploma Verification
Description:  Provides a shortcode to display diploma validation form
Version:      1.0.1
Author:       Rosa Maggi
License: Copyright (c) 2021 Ecole Polytechnique Federale de Lausanne, Switzerland
*/

function epfl_diploma_verification_process_shortcode($atts)
{
    ob_start();

    $atts = shortcode_atts([
        'language' => ''
    ], $atts);
    if ($atts['language'] == 'FR') {
        $language = 'fr';
        $labels = [
            'graduateName' => 'Nom du diplômé',
            'graduateFirstName' => 'Prénom du diplômé',
            'graduateSurname' => 'Nom du diplômé',
            'documentNumber' => 'Numéro du diplôme',
            'validate' => 'Valider',
            'documentTitle' => 'Titre du document',
            'error' => 'Impossible de vérifier le document',
            'errorMessage' => 'Merci de contacter le <a href="https://studying.epfl.ch/student_desk">Guichet des étudiants</a>',
            'authenticationConfirmed' => 'Authenticité confirmée'
        ];

    } else {
        $language = 'en';
        $labels = [
            'graduateName' => 'Graduate’s Name',
            'graduateFirstName' => 'Graduate’s First Name',
            'graduateSurname' => 'Graduate’s Surname',
            'documentNumber' => 'Document Number',
            'validate' => 'Validate',
            'documentTitle' => 'Document Title',
            'error' => 'Unable to verify document',
            'errorMessage' => 'Please contact the <a href="https://studying.epfl.ch/student_desk">Student Services Desk</a>',
            'authenticationConfirmed' => 'Authenticity confirmed'
        ];
    }

    wp_enqueue_style( 'epfl_diploma_verification_style', plugin_dir_url(__FILE__).'css/styles.css', [], '2.1');

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST["prenom"])) 
        {
            $prenom = $_POST["prenom"];
        } 
        else 
        {
            $prenom = null;
        }
        if (isset($_POST["nom"])) 
        {
            $nom = $_POST["nom"];
        } 
        else 
        {
            $nom = null;
        }
        if (isset($_POST["diplome"])) 
        {
            $diplome = $_POST["diplome"];
        } 
        else 
        {
            $diplome = null;
        }

        include('diploma_verification_form.php');
        call_web_service($prenom, $nom, $diplome, $labels);
        return ob_get_clean();
    } else {
        include('diploma_verification_form.php');
        $output = ob_get_contents();
        ob_end_clean();
        return $output;
    }
}

function call_web_service($prenom, $nom, $diplome, $labels){
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
        success($data, $labels);
    }else{
        failure($labels);
    }
    curl_close($curl);
}

function success($data, $labels){
    ?>
    <div id="success" class="row">
        <div class="status-success col-lg-12">
            <div class="row">
                <div class="col-lg-12">
                    <h3><label><?php echo $labels['authenticationConfirmed']; ?></label></h3>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-5"><label><?php echo $labels['graduateName']; ?></label></div>
                <div id="r-nom" class="col-lg-7"><?php echo $data[0]['fullName'] ?></div>
            </div>
            <div class="row">
                <div class="col-lg-5"><label><?php echo $labels['documentNumber']; ?></label></div>
                <div id="r-diplome" class="col-lg-7"><?php echo $data[0]['numero'] ?></div>
            </div>
            <div class="row">
                <div class="col-lg-5"><label><?php echo $labels['documentTitle']; ?></label></div>
                <div id="r-titre" class="col-lg-7"><?php echo $data[0]['title'] ?></div>
            </div>
        </div>
    </div>
    <?php
}

function failure($labels){
    ?>
    <div id="failure" class="row">
        <div class="status-failure col-lg-12">
            <h3><?php echo $labels['error']; ?></h3>
            <div class="row">
                <div class="col-lg-12"><?php echo $labels['errorMessage']; ?></div>
            </div>
        </div>
    </div>
    <?php
}

add_action( 'init', function() {
    add_shortcode('epfl_diploma_verification', 'epfl_diploma_verification_process_shortcode');
});
