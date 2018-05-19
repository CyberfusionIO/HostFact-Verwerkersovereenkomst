# Description

This HostFact plugin does three things:

- your debtors are able to agree to your DPA (verwerkersovereenkomst) in the HostFact `klantenpaneel`;
- once the debtor has accepted the DPA, a confirmation email will be sent via HostFact;
- (optional) as long as a debtor hasn't signed the DPA, a message can be shown in the `klantenpaneel`

# Screenshot

![DPA plugin](https://i.imgur.com/wtMLjBs.png)


# (English) Installation steps

**HostFact:**
1. Create a custom text field, called 'DPA' for example, in HostFact by heading to /Pro/customclientfields.php?page=add. Write down the 'Veldcode' you entered.
2. Create an email template by navigating to /Pro/templates.php?page=email. This email will be sent to your debtor after they have agreed to the DPA. Once it's saved, click on the newly created email template. In the URL you find the template ID at the end. It's shown in the URL like: &id=6. In this case, the ID is 6, write that down.

**FTP:**
1. Download and unpack the ZIP: https://github.com/CyberfusionNL/HostFact-Verwerkersovereenkomst/archive/master.zip
2. After unpacking the ZIP, click the folder 'klantenpaneel' and continue by clicking the folder 'custom'.
3. Upload the entire 'plugins' folder to your your own '/klantenpaneel/custom/' directory on your server.
4. Open the config file '/klantenpaneel/custom/plugins/dpa/config.php'. Change all example 'replaceme' values with the correct values.
5. Finally, upload the PDF containing your DPA to the folder '/klantenpaneel/custom/plugins/dpa/docs/'. Make sure the file name is entered correctly in your config file.

*Note: if no PDF has been uploaded 'docs' folder and/or entered in the config file, debtors will see a message saying that the DPA can be signed soon in the `klantenpaneel`.*

# (Dutch) Installatiestappen

**HostFact:**
1. Maak een custom text field, bijvoorbeeld genaamd 'DPA', aan in HostFact door te navigeren naar /Pro/customclientfields.php?page=add. Noteer de door jou ingevoerde veldcode.
2. Creeër een email template door te navigeren naar /Pro/templates.php?page=email. Deze e-mail wordt naar de debiteur verstuurd na het accepteren. Zodra het email template is opgeslagen, vind je aan het einde van de URL het template ID. Bijvoorbeeld: "&id=6" (zonder ""). Het ID is in dit geval 6. Noteer deze.

**FTP:**
1. Download en pak de ZIP uit: https://github.com/CyberfusionNL/HostFact-Verwerkersovereenkomst/archive/master.zip
2. Nadat de ZIP is uitgepakt: open de map 'klantenpaneel', en open vervolgens de map 'custom'.
3. Upload de volledige map 'plugins' naar je eigen '/klantenpaneel/custom/' map.
4. Open het configuratiebestand '/klantenpaneel/custom/plugins/dpa/config.php'. Wijzig de voorbeeldwaarden 'replaceme' met de correcte waarden.
5. Upload tot slot de PDF met de DPA naar de map '/klantenpaneel/custom/plugins/dpa/docs/'. Bevestig dat de naam van het PDF-bestand correct is opgegeven in het configuratiebestand.

*Let op: als er geen PDF is geüpload naar de map 'docs' en/of opgegeven in het configuratiebestand, dan zien debiteuren in het `klantenpaneel` een bericht dat de DPA binnenkort getekend kan worden.*

# Optional: Ask debtors to sign
Asking debtors to accept the DPA plugin throughout the HostFact `klantenpaneel`:

![Asking debtors to accept](https://i.imgur.com/LX3OR9A.png)

You can use the following code in your custom/views/header.phtml to show a message to all debtors that haven't signed the DPA yet in the `klantenpaneel`. Below code will check if the debtor has agreed to the DPA yet, and if not, a message will be shown.

    <?php
    $dpa = new Dpa\Dpa_Model();

    if ($dpa->debtorDPAStatus() == '' && strpos($_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'], __('dpa', 'url', 'dpa')) == false) {
        echo '<div class="alert alert-warning" role="alert"><p>'.__('dpa not accepted').' <a href="/klantenpaneel/'.__('dpa', 'url', 'dpa').'/">'.__('accept').'</a></p></div>';
    }
    ?>

# Delete DPA preference (for testing)

If you're testing and you need to delete the custom field value for a debtor, you can either delete the value or use this SQL query:

    delete FROM `HostFact_Debtor_Custom_Values` WHERE ReferenceID = {DEBTORID} and FieldID = {FIELDID};

Replace {DEBTORID} with the debtor ID (NOT the debtor code) and {FIELDID} with the same field ID that you set in the config.

# Known bug

When a user resets their password and logs in after doing so, the message above (under "Optional: Ask debtors to sign") is not shown, and the module says that the DPA has already been signed for that account. That is because the custom field for that debtor is created and we only check if the custom field was created; not what it contains. I'm not aware of any good method to fix that, currently.
