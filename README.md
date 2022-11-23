# Tasdid Module OpenCart 3.x.x

 Payment module for OpenCart for [Tasdid platform](https://tasdid.net/).

## Rrequirements

1-Active or demo merchant account(You can get it by registering on the [Tasdid platform](https://tasdid.net/).

2-OpenCart version 3.x.x or higher

 

## Upload the module

A-If your version of OpenCart supports ocmod, you can download this file [tasdid-v1.0.ocmod.zip](https://github.com/muamal2/tasdid_module_opencart3.x.x/raw/main/tasdid-v1.0.ocmod.zip)

### Note: You can upload by going to admin>module>Module installer.

B-If your OpenCart does not support ocmod, you can download [tasdid-v1.0.zip](https://github.com/muamal2/tasdid_module_opencart3.x.x/raw/main/tasdid-v1.0.zip) this file and transfer it via FTP and then to the root file of your site and extract file.

### Note: After the upload process is complete, you must go to admin>module>Managing modifications, and click on the refresh button.

Congratulation !!

Uploaded successfully, make sure everything is OK, you can go to admin>module>module>payment method 

If you see [tasdid](https://tasdid.net/) in it, the upload process was successful


##  Set module settings

1-After making sure that the module has been uploaded, click on ✚ to activate the module

2-Click on its icon 🖋️ For the purpose of displaying the module settings, you will see each of the:

- Email and password (provide by [tasdid](https://tasdid.net/)).

- The service number that will be used for payment(It is brought from the merchant's account in the [Tasdid platform](https://tasdid.net/).

- Guidance link after payment(Put a link to be directed to after successful payment).

- Work environment(Depending on the type of account for the merchant, if it is a demo, choose test).

- Iraqi dinar exchange rates(Put the exchange rate against each currency in your store, and it must be an integer, for example, 1500, without commas).

- The total required to activate the payment method(For example, if the value of the order in the basket is 50,000, the payment gateway will appear, and if not, it will not appear to the customer)(Leave it empty to disable it).

- Order status before and after payment(Be careful in this choice because it will cause you losses if you do not choose correctly).

  For example \\ before payment put it pending and after payment put it
 complete
- Geographical area(For example, you are asking for a product customer, who is Baghdad, so he can pay with [tasdid](https://tasdid.net/). As for non-regions, the [tasdid](https://tasdid.net/) is not shown to him)(Multiple zones can be set or disabled)

- debug logging(It records errors in the module)

- module status(It can be turned on or off temporarily or permanently).

- Sort order(You have more than one payment gateway and you want the tasdid payment gateway to appear in the first in the sequence. You can put 1).

## Common errors
* Bad Username or password.... 

  Something is wrong Make sure you type the email and password correctly.
* Invalid service>....

  The service number is incorrect or not found in the merchant account database.

* Invalid url....

  The link is invalid (it is recommended to put an internal link within the site, not an external one).

## Help Center
* For quick communication, call 422
* To create an account or to solve a specific problem, you can contact the [Tasdid platform](https://tasdid.net/).
* To contact the module developer [Muamal Helali](https://t.me/mmamm12).

## Warning

* This module was programmed by [Muamal Helali](https://github.com/muamal2) and was checked by the [Tasdid platform](https://tasdid.net) team and Approved

  If you see another module with the same work, or you programmed it with the same mechanism as the module, we recommend that you consult the [Tasdid platform team](https://tasdid.net) to be checked for safety purposes.

## License

[Apache License 2.0](https://choosealicense.com/licenses/apache-2.0/)
