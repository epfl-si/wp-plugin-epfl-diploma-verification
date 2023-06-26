<?php
/*
Plugin Name:  EPFL Diploma Verification
Plugin URI:   https://wp-httpd/rosa
Description:  Provides a shortcode to display diploma validation form
Version:      0.1
Author:       Rosa Maggi
License: Copyright (c) 2021 Ecole Polytechnique Federale de Lausanne, Switzerland
*/

function epfl_diploma_verification_process_shortcode()
{
    ob_start(); // Start output buffering
    // Check if the form is submitted and process the data
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $prenom = $_POST["prenom"];
        $nom = $_POST["nom"];
        $diplome = $_POST["diplome"];

        // Return the response
        include('diploma_verification_form.php');
        call_web_service($prenom, $nom, $diplome);
        // End output buffering and return the buffered content
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

    // Serialize the form data
    $serializedData = http_build_query($formData);

    // Construct the URL with query parameters
    $urlWithParams = $apiUrl . '?' . $serializedData;
    echo $urlWithParams;

    // Initialize cURL
    $curl = curl_init();
    // Set the cURL options
    curl_setopt($curl, CURLOPT_URL, $urlWithParams);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

    // Execute the cURL request
    $response = curl_exec($curl);

    echo "  xcvxcvxcv " .$response;

    // Check for cURL errors
    if ($response === false) {
        $error = curl_error($curl);
        curl_close($curl);
        die('cURL error: ' . $error);

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
    }else{
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
                        <div id="r-nom" class="col-lg-7"></div>
                    </div>
                    <div class="row">
                        <div class="col-lg-5"><label>Document Number</label></div>
                        <div id="r-diplome" class="col-lg-7"></div>
                    </div>
                    <div class="row">
                        <div class="col-lg-5"><label>Document Title</label></div>
                        <div id="r-titre" class="col-lg-7"></div>
                    </div>
                </div>
            </div>
        <?php
    }

    // Close cURL
    curl_close($curl);

    // Process the response
    $result = json_decode($response, true);

    // Check if the response was successfully parsed
    if ($result === null) {
        die('Error decoding JSON response');
    }

    // Use the result
    var_dump($result);

}

add_shortcode('epfl_diploma_verification', 'epfl_diploma_verification_process_shortcode');
