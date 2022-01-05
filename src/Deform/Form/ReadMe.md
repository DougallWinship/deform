# Form
Forms are build and processed according to a FormModel.

There are various components to achieve this which are all dependant upon the FormModel instance:

FormBuilder - builds (and populates) a form according to the FormModel definition

FormProcessor - process the GET/POST data and acts accordingly

FormData - a container for form data, validation errors, submission status etc

Form - a convenience for tying all the above together, represents the entire form life-cycle

## FormModel
The form model is responsible for providing details of
- fields
- field validation
- defaults on how the form should be processed

To this end there is a FormModel base class which provides sensible defaults (and can be used standalone for basic scenarios)

There is also a FormModelGenerator which allows for auto generation of FormModel instances via adapters from various standard providers
- manual definition
- ORMs
- DAOs

