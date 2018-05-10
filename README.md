# Description

This HostFact plugin does three things:
- your debtors are able to agree to the DPA (verwerkersovereenkomst) created by your company in the HostFact `klantenpaneel`;
- once the debtor has accepted the DPA, an email will be sent via HostFact;
- additionally, as long as a debtor hasn't signed the DPA, a message can be shown in the `klantenpaneel`

# Screenshots

Plugin:

![DPA plugin](https://i.imgur.com/wtMLjBs.png)

Asking debtors to accept the DPA plugin throughout the HostFact `klantenpaneel`:

![Asking debtors to accept](https://i.imgur.com/LX3OR9A.png)

# Install

Note: this documentation, and the plugin, assumes `klantenpaneel` as the directory that the `klantenpaneel` is stored in. If you have it in a different directory, or in `/`, simply `grep` through the code and remove or alter `klantenpaneel` so that only `dpa/` or `/dpa` is left.

In /Pro:

- Create a custom field in HostFact on /Pro/customclientfields.php?page=add . Use 'DPA' in capitals (without '') as field code ('Veldcode');
- Create an empty email template by navigating to /Pro/templates.php?page=email, clicking 'Template toevoegen', selecting 'een standaard template' under "Wat voor template wilt u toevoegen?" and clicking 'Bevestigen'. As both template name and subject, use 'dummy'. Leave the body empty. Once it's saved, click on the newly created email template and look at the email template in the URL. It's shown in the URL like: &id=6 (the ID is 6, write that down)

In FTP:

- Upload this plugin to klantenpaneel/custom/plugins
- In the file models/dpa_model.php you should change "FieldID = REPLACEME" (line 76) to "FieldID = x" where x is the appropriate field code. The field code is shown in the URL when you created your custom field in /Pro (last number in the URL);
- In the file models/dpa_model.php you should change 'Subject' (line 50) to the subject that you want to use for the email a debtor receives once the preference has been set on their account;
- In the file models/dpa_model.php you should change 'TemplateID' (line 51) to the template ID for the email template that you created a few steps ago;
- In the file models/dpa_model.php you should change 'Message' to the email that you want to send to the debtor;
- Finally, upload a PDF containing your DPA to the folder docs/ called 'dpa.pdf'

# Optional: Ask debtors to sign

You can use the following code in your custom/views/header.phtml to show a message to all debtors that haven't signed the DPA yet in the `klantenpaneel`. Below code will check if the debtor has agreed to the DPA yet, and if not, a message will be shown.

    <?php
    $dpa = new Dpa\Dpa_Model();

    if ($dpa->getPreference() == false && strpos('https://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'], 'dpa') == false) {
        echo '<div class="alert alert-warning" role="alert"><p>'.__('dpa not accepted').' <a href="/klantenpaneel/dpa">'.__('accept').'</a></p></div>';
    }
    ?>

# Delete preference

If you're testing and you need to delete the custom field value for a debtor, you can either delete the value, but you can also use this SQL query:

    delete FROM `HostFact_Debtor_Custom_Values` WHERE ReferenceID = {DEBTORID} and FieldID = {FIELDID};

Replace {DEBTORID} with the debtor ID (NOT the debtor username!) and {FIELDID} with the same field ID that you set in the code.
