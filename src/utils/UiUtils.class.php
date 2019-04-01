<?php
namespace framework\utils;

/**
 * Classe utilitaire pour la création des interfaces utilisateur
 *
 */
class UiUtils {

	/**
	 * Permet de créer une UListe depuis une liste de données
	 *
	 * @param string[] $list
	 */
	public static function createUList(array $list){
		?>
	    <ul>
    	    <?php
    	    foreach ($list as $item) {
    	        ?>
    	        <li><?php echo $item; ?></li>
    	        <?php
    	    }
    	    ?>
	    </ul>
	    <?php
	}

	public static function createAlert(string $type, string $message) {
		?>
			<div class="alert alert-<?php echo $type; ?> alert-dismissible fade show" role="alert">
			  <?php echo $message; ?>
			  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
			    <span aria-hidden="true">&times;</span>
			  </button>
			</div>
		<?php
	}
}