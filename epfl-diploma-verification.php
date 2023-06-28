<?php
/*
Plugin Name:  EPFL Diploma Verification
Description:  Provides a shortcode to display diploma validation form
Version:      1.0.1
Author:       Rosa Maggi
License: Copyright (c) 2021 Ecole Polytechnique Federale de Lausanne, Switzerland
*/

function epfl_diploma_verification_process_shortcode()
{  
    ob_start();
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
        call_web_service($prenom, $nom, $diplome);
        return ob_get_clean();
    } else {
        include('diploma_verification_form.php');
        $output = ob_get_contents();
        ob_end_clean();
        return $output;
    }
}

function call_web_service($prenom, $nom, $diplome){
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
    }else{
        failure();
    }
    curl_close($curl);
}

function success($data){
    ?>
    <div id="success" class="row">
        <div class="status-success col-lg-12">
            <div class="row">
                <div class="col-lg-12">
                    <h3>Authenticity confirmed</h3>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-5"><label>Graduate’s Name</label></div>
                <div id="r-nom" class="col-lg-7"><?php echo $data[0]['fullName'] ?></div>
            </div>
            <div class="row">
                <div class="col-lg-5"><label>Document Number</label></div>
                <div id="r-diplome" class="col-lg-7"><?php echo $data[0]['numero'] ?></div>
            </div>
            <div class="row">
                <div class="col-lg-5"><label>Document Title</label></div>
                <div id="r-titre" class="col-lg-7"><?php echo $data[0]['title'] ?></div>
            </div>
        </div>
    </div>
    <?php
}

function failure(){
    ?>
    <div id="failure" class="row">
        <div class="status-failure col-lg-12">
            <h3>Unable to verify document</h3>
            <div class="row">
                <div class="col-lg-12">Please contact the <a href="https://studying.epfl.ch/student_desk">Student Services Desk</a></div>
            </div>
        </div>
    </div>
    <?php
}

add_action( 'init', function() {
    add_shortcode('epfl_diploma_verification', 'epfl_diploma_verification_process_shortcode');
});
