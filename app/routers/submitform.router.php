<?php
use generators\DbConversor as DbConversor;
use lib\Email as Email;
use lib\Config;

// /submitform/:id controller
// Submit form controller
// A form gets submitted and you get redirected where the form says 

// TODO: Validate different field types

$app->post('/submitform/:id', function ($id) use ($app) {
    
    $c = array();
    $parameters = array();
   
    $post_vars = $app->request()->post();
    

    // We grab the form
    $form = models\Form::find($id);

    // We grab its fields
    $fields = $form->fields;

    // We grab its contacts
    $contacts = $form->contacts;

    // We add a response
    $response = new models\ModelBuilder;
    $response::fromTable('responses_'.$id);
    $response->form_id = $id;

    // This is the array containing the form fields and their values
    // that will be passed to the twig template for them to be sent by e-mail
    $parameters['fields'] = array();

    // We have to iterate through the form fields
    foreach($post_vars as $key=>$var)
    {
        // We skip everything that is not a field
        // as field lengths for example
        if (substr($key, 0, 6) != 'field_') continue;

        $id_field = end(explode('_',$key));

        // We check that it's in the required format
        if ((int)$id_field != $id_field) continue;

        // if the field submitted exists within the form's scope
        $form_field = $fields->find($id_field);

        if (!$fields->find($id_field)) continue;

        // Now we know $key is valid, we can assign it
        // We use a helper to assist us in the different use cases we can 
        // encounter where the type of input we want to create gives as the value in a format
        // different than we want to store (e.g. Checkbox gives 'on' and we want to store 1)
        $value_inserted = DbConversor::convert($form_field->getTypeString(), $var);

        $response->$key = $value_inserted;

        // We also add it to our parameters array for it to be send to the contact by 
        // e-mail
        $parameters['fields'][$key] = $value_inserted;
        // And the field names
        $parameters['field_names'][$key] = $form_field->field_name; 
    }

    $response->save();

    // Aaaand, we send emails to all the contacts of that form

    $twig = $app->view()->getEnvironment(); // twig environment
    $transport = Swift_MailTransport::newInstance(); // Create the Mailer using your created Transport 
    $mailer = Swift_Mailer::newInstance($transport);

    $parameters['form'] = $form;

    foreach ($contacts as $contact) {

        // Contact to be accessible from the template
        $parameters['contact'] = $contact;

        $generator = new Email($twig);
        $message = $generator->getMessage(Config::read('email_template'), $parameters);
        $message->setTo($contact->contact_email);
        $message->setFrom(Config::read('email_from'));

        $mailer->send($message);

    }
    
    $app->redirect($form->redirect);

});

