# Description

This HostFact plugin does three things:
- your debtors are able to agree to the DPA (verwerkersovereenkomst) created by your company in the HostFact `klantenpaneel`;
- once the debtor has accepted the DPA, an email will be sent via HostFact;
- additionally, as long as a debtor hasn't signed the DPA, a message can be shown in the `klantenpaneel`

# Todo
- [x] Save date and IP address instead of 'yes' in custom field
- [x] Do error handling before sending confirmation email to the debtor
- [x] Create config file

# Screenshots

Plugin:

![DPA plugin](https://i.imgur.com/wtMLjBs.png)


# Installation steps

HostFact:
1. Create a custom text field, for example 'DPA', in HostFact by navigation to /Pro/customclientfields.php?page=add. Write down your entered 'Veldcode'.
2. Create an email template by navigating to /Pro/templates.php?page=email. This email will be send to your debtor after agreeing. Once it's saved, click on the newly created email template. In the URL you find the template id. It's shown in the URL like: &id=6. The ID is 6, write that down.

FTP:
1. Download and unpack the ZIP: https://github.com/CyberfusionNL/HostFact-Verwerkersovereenkomst/archive/master.zip
2. After unpacking the ZIP click on the folder 'klantenpaneel', continue clicking on the folder 'custom'.
3. Upload the complete 'plugins' folder to your your own '/klantenpaneel/custom/' directory on your server.
4. Open the config file: '/klantenpaneel/custom/plugins/dpa/config.php'. Change all 'replaceme' by the correct value.
5. Finally, upload the PDF containing your DPA to the folder '/klantenpaneel/custom/plugins/dpa/docs/'. Make sure the filename is exactly as entered in your config file.

Note 2: if no PDF has been uploaded 'docs' folder and entered in the config file, visitors of the `klantenpaneel` will see a message saying that the DPA can be signed soon.

# Optional: Ask debtors to sign
Asking debtors to accept the DPA plugin throughout the HostFact `klantenpaneel`:

![Asking debtors to accept](https://i.imgur.com/LX3OR9A.png)

You can use the following code in your custom/views/header.phtml to show a message to all debtors that haven't signed the DPA yet in the `klantenpaneel`. Below code will check if the debtor has agreed to the DPA yet, and if not, a message will be shown.

    <?php
    $dpa = new Dpa\Dpa_Model();

    if ($dpa->debtorDPAStatus() == '' && strpos('https://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'], __('dpa', 'url', 'dpa')) == false) {
        echo '<div class="alert alert-warning" role="alert"><p>'.__('dpa not accepted').' <a href="/klantenpaneel/'.__('dpa', 'url', 'dpa').'/">'.__('accept').'</a></p></div>';
    }
    ?>

# Delete preference (for testing)

If you're testing and you need to delete the custom field value for a debtor, you can either delete the value or use this SQL query:

    delete FROM `HostFact_Debtor_Custom_Values` WHERE ReferenceID = {DEBTORID} and FieldID = {FIELDID};

Replace {DEBTORID} with the debtor ID (NOT the debtor code) and {FIELDID} with the same field ID that you set in the config.

# Known bug

When a user resets their password and logs in after doing so, the message above (under "Optional: Ask debtors to sign") is not shown, and the module says that the DPA has already been signed for that account. That is because the custom field for that debtor is created and we only check if the custom field was created; not what it contains. I'm not aware of any good method to fix that, currently.
