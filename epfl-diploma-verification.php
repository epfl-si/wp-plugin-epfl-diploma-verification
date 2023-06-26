<?php
/*
Plugin Name:  EPFL Diploma Verification
Plugin URI:   https://wp-httpd/rosa
Description:  Provides a shortcode to display diploma validation form
Version:      0.1
Author:       Rosa Maggi
License: Copyright (c) 2021 Ecole Polytechnique Federale de Lausanne, Switzerland
*/

function epfl_diploma_verification_process_shortcode() {
    //ob_start(); // Start output buffering
    // Check if the form is submitted and process the data
    if(isset($_POST['SubmitButton'])){
        $prenom = $_POST["prenom"];
        $nom = $_POST["nom"];
        $diplome = $_POST["diplome"];

        call_web_service($prenom, $nom, $diplome);

    }
?>
        <div>
            <form method="post" action="" id="verification-form">
                <div class="form-group">
                    <label for="prenom">Graduate’s First Name</label><br>
                    <input  id="prenom" class="form-control" name="prenom" type="text" placeholder="First name..." value="<?php echo $_POST["prenom"]; ?>">
                </div>
                <div class="form-group">
                    <label for="nom">Graduate’s Surname</label><br>
                    <input id="nom" class="form-control" name="nom" type="text" placeholder="Surname..."value="<?php echo $_POST["nom"]; ?>">
                </div>
                <div class="form-group">
                    <label for="diplome">Document Number</label><br>
                    <input id="diplome" class="form-control" name="diplome" type="text" placeholder="Number..."value="<?php echo $_POST["diplome"]; ?>">
                </div>
                <p>
                    <input type="submit" name="SubmitButton"  class="btn btn-primary"  value="Validate">
                </p>
            </form>
        </div>
    <div id="success" class="row" style="display: none;">
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
    <div id="failure" class="row" style="display: none;">
        <div class="status-failure col-lg-12">
            <h3>Unable to verify document</h3>
            <div class="row">
                <div class="col-lg-12">Please contact the <a href="https://studying.epfl.ch/student_desk">Student Services Desk</a></div>
            </div>
        </div>
    </div>
<?php
    echo $prenom;
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
    // REST API endpoint URL - /services/diplome/0063459/validate?prenom=Emma%20Charlotta&nom=Kallstig&diplome=0063459
    echo $urlWithParams;

    // Initialize cURL
    $curl = curl_init();

    // Set the cURL options
    curl_setopt($curl, CURLOPT_URL, $urlWithParams);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

    // Execute the cURL request
    $response = curl_exec($curl);

    // Check for cURL errors
    if ($response === false) {
        $error = curl_error($curl);
        curl_close($curl);
        die('cURL error: ' . $error);
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
