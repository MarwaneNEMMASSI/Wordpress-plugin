<?php
/*
*Plugin Name: Contact
*Description: Just another contact form plugin.
*/

    //s'éxècute lors de l'activation du plugin
    function Activer_plugin()
        {
            //création de la base de donnée 
            global $wpdb;
            $Table_name = $wpdb->prefix . 'contact_form';
            $charset_collate = $wpdb->get_charset_collate();

            $sql = "CREATE TABLE $Table_name (
                id int(11) NOT NULL AUTO_INCREMENT,
                nom varchar(20) NOT NULL DEFAULT '',
                prenom varchar(20) NOT NULL DEFAULT '',
                email varchar(20) NOT NULL DEFAULT '',
                sujet varchar(20) NOT NULL DEFAULT '',
                message varchar(100) NOT NULL DEFAULT '',
                date datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
                PRIMARY KEY (id)
            ) $charset_collate;";

            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta($sql);
        }
        
        //Activation du plugin
        register_activation_hook(__FILE__, 'Activer_plugin');

    // s'éxècute lors de la désactivation du plugin
    function Desactiver_plugin()
        {
            //Suppression de la base de donnée
            global $wpdb;
            $Table_name = "wp_contact_form";
            $sql = "DROP TABLE IF EXISTS $Table_name;";
            $wpdb->query($sql);
        }

        // désactivation du plugin
        register_deactivation_hook(__FILE__, 'Desactiver_plugin');

    //Affichage du formulaire
    function form_shortcode()
        {
            $form = '<form method="post" action="">
                    <label for = "prenom">Prénom</label>
                    <input type = "text" name = "prenom" id = "prenom" required>
                    <label for = "nom">Nom</label>
                    <input type = "text" name = "nom" id = "nom" required>
                    <label for = "email">Email</label>
                    <input type = "email" name = "email" id = "email" required>
                    <label for = "sujet">Sujet</label>
                    <input type = "text" name = "sujet" id = "sujet" required>
                    <label for = "message">Message</label>
                    <textarea name = "message" id = "message" rows = "10" required></textarea>
                    <input type = "submit" name = "submit" >
                    </form>';
            return $form;
        }

    //Initialisation du shortcode concernant le formulaire
    add_shortcode('form', 'form_shortcode');
            
            //Traitement du formulaire 
            if (isset($_POST['submit']))
                {
                    $FirstName = sanitize_text_field($_POST['prenom']);
                    $LastName = sanitize_text_field($_POST['nom']);
                    $Email = sanitize_email($_POST['email']);
                    $Subject = sanitize_text_field($_POST['sujet']);
                    $Message = sanitize_text_field($_POST['message']);

                    global $wpdb;
                    $Table_name = $wpdb->prefix . 'contact_form';

                    $wpdb->insert(
                    $Table_name,
                    array(
                        'prenom' => $FirstName,
                        'nom' => $LastName,
                        'email' => $Email,
                        'sujet' => $Subject,
                        'message' => $Message,
                    ),
                    array('%s', '%s', '%s', '%s', '%s')
                    );            
                }
        
    function menu_page()
        {
            add_menu_page('contact-form', 'contact-form', 'manage_options', 'cf_responses_page', 'settings_page', 'dashicons-email-alt', 1);
        }
            add_action('admin_menu', 'menu_page');

    function settings_page()
        {
            if (!current_user_can('manage_options')) {
            return;
        }

            global $wpdb;
            $table_name = $wpdb->prefix . 'contact_form';
            $results = $wpdb->get_results("SELECT * FROM $table_name");

            echo '<div class="wrap bg-dark">';
            echo '<h1>' . esc_html__('Contact Form Responses', 'contact-form') . '</h1>';
            echo '<p>' . esc_html__('View and manage responses submitted through the contact form.') . '</p>';
            echo '<table class="wp-list-table widefat fixed striped">';
            echo '<thead>';
            echo '<tr>';
            echo '<th style="width: 2rem;">' . esc_html__('ID', 'contact-form') . '</th>';
            echo '<th>' . esc_html__('First name', 'contact-form') . '</th>';
            echo '<th>' . esc_html__('Last name', 'contact-form') . '</th>';
            echo '<th>' . esc_html__('Email', 'contact-form') . '</th>';
            echo '<th>' . esc_html__('Subject', 'contact-form') . '</th>';
            echo '<th>' . esc_html__('Message', 'contact-form') . '</th>';
            echo '<th>' . esc_html__('Date', 'contact-form') . '</th>';
            echo '</tr>';
            echo '</thead>';
            echo '<tbody>';

    foreach ($results as $row) 
        {
            echo '<tr>';
            echo '<td>' . $row->id . '</td>';
            echo '<td>' . $row->nom . '</td>';
            echo '<td>' . $row->prenom . '</td>';
            echo '<td>' . $row->email . '</td>';
            echo '<td>' . $row->sujet . '</td>';
            echo '<td>' . $row->message . '</td>';
            echo '<td>' . $row->date . '</td>';
            echo '</tr>';
        }
            echo '</tbody>';
            echo '</table>';
            echo '</div>';
        }
?>