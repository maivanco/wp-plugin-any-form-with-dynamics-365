User story: When someone fills in the form with the checked option to subscribe to receive the newsletter, this plugin will push the subscriber to the Dynamics 365 Marketing List.


# General Settings

- Access ITC Dynamics 365 from the admin panel.
    - Fill in the link to the CRM URL, username & password to connect Dynamics 365.
    - Select a marketing list that you would like to contain a list of subscribers when someone submits the forms from your website.

# Gravity Form Configurations

- Put the specific class with format below to push data from current form to Dynamics 365 Marketing List
    - `map-d365-field-[dynamics_365_entity_form_field]`
    - For example `map-d365-field-emailaddress1`, the `emailaddress1` field name is the name from Dynamics 365 Form Field
    - This class means that the value of the current field will be inserted to the corresponding field name in the Marketing List.
    - If your form has `Consent` field type, users on the website must agree by selecting the checkbox to let the form push data from website to Marketing List, otherwise there is nothing will happen