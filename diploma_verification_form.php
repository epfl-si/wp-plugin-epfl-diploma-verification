<div>
    <form method="post" id="verification-form" action="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>">
        <div class="form-group">
            <label for="prenom"><?php _e('graduateFirstName','epfl-diploma-verification'); ?></label><br>
            <input  id="prenom" class="form-control" name="prenom" type="text" placeholder="<?php _e('graduateFirstNamePlaceHolder','epfl-diploma-verification'); ?>" value="<?php echo isset($_POST["prenom"]) ? esc_attr($_POST["prenom"]) : ''; ?>">
        </div>
        <div class="form-group">
            <label for="nom"><?php _e('graduateSurname','epfl-diploma-verification'); ?></label><br>
            <input id="nom" class="form-control" name="nom" type="text" placeholder="<?php _e('graduateSurnamePlaceHolder','epfl-diploma-verification'); ?>" value="<?php echo isset($_POST["nom"]) ? esc_attr($_POST["nom"]) : ''; ?>">
        </div>
        <div class="form-group">
            <label for="diplome"><?php _e('documentNumber','epfl-diploma-verification'); ?></label><br>
            <input id="diplome" class="form-control" name="diplome" type="text" placeholder="<?php _e('documentNumberPlaceHolder','epfl-diploma-verification'); ?>" value="<?php echo isset($_POST["diplome"]) ? esc_attr($_POST["diplome"]) : ''; ?>">
        </div>
        <p>
            <input type="submit" name="SubmitButton"  class="btn btn-primary"  value="<?php _e('validate','epfl-diploma-verification'); ?>">
        </p>
    </form>
</div>
