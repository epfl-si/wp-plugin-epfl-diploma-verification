<div>
    <form method="post" id="verification-form" action="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>">
        <div class="form-group">
            <label for="prenom">Graduate’s First Name</label><br>
            <input  id="prenom" class="form-control" name="prenom" type="text" placeholder="First name..." value="<?php echo isset($_POST["prenom"]) ? esc_attr($_POST["prenom"]) : ''; ?>">
        </div>
        <div class="form-group">
            <label for="nom">Graduate’s Surname</label><br>
            <input id="nom" class="form-control" name="nom" type="text" placeholder="Surname..." value="<?php echo isset($_POST["nom"]) ? esc_attr($_POST["nom"]) : ''; ?>">
        </div>
        <div class="form-group">
            <label for="diplome">Document Number</label><br>
            <input id="diplome" class="form-control" name="diplome" type="text" placeholder="Number..." value="<?php echo isset($_POST["diplome"]) ? esc_attr($_POST["diplome"]) : ''; ?>">
        </div>
        <p>
            <input type="submit" name="SubmitButton"  class="btn btn-primary"  value="Validate">
        </p>
    </form>
</div>