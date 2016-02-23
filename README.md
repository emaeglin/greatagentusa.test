# greatagentusa.test

Demo: http://emaeglin.com/

- __www/__
  - __index.html__ : Main HTML file with registration form
  - __js/__
    - __phonenumbers/__ 
      - __chekck.js__   : JS phone validation function
      - __compiled.js__ : http://closure-compiler.appspot.com/home - compiled js libraries for phone validation
    - __main.js__ : Main JS file
  - __api/__
      - __lead.php__    : Lead controller
      - __twilio/__      : Twilio api: callback, voice, etc...
- __core/__
  - __config.php__      : Config file
  - __init.php__
  - __includes/__
    - __ClassCall.php__ : Class for Call create
    - __ClassDb.php__ : Class for working with DB
    - __ClassLead.php__ : Class for Lead create
    - __ClassLead.php__ : Class for work with Leads waiting for calling
    - __ClassPage.php__ : Class for pages rendering
    - __ClassPhone.php__ : Class for phone validation
- __tests/__
    - __CallTest.php__
    - __DBTest.php__
    - __LeadTest.php__
    - __PageTest.php__
    - __PhoneTest.php__
- __commands/__
    - __call.php__  : file to cron job: auto calling