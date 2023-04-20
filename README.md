# Ualá Bis for Magento 2

This Extension is used to make payments using Ualá Bis API in Argentina.

<img align="right" src="https://i.imgur.com/deVMob1.png" width="400px" alt="magento logo">

- Allow end user set credentials, checkout message and name of payment method 
- Generate pending orders and using return parameters back and change to order status cancel or processing (and create invoice).
- Add validation to prevent unexpected changes on status using urls.
- Can be used in production / test mode just changing credentials.


## Manual Installation

- Create a folder [root]/app/code/Uala/Bis.
- Download module ZIP from <a href="https://github.com/Uala-Developers/ualabis-magento/archive/refs/heads/main.zip">HERE</a>.
- Copy to folder.
- Check module structure for these files: app/code/Uala/Bis/registration.php and others.
- Check module structure for this folder: app/code/Uala/Bis/Model/ app/code/Uala/Bis/Block/ app/code/Uala/Bis/etc/ and others.

Then you'll need to activate the module.

```bash
bin/magento setup:upgrade
bin/magento setup:di:compile
bin/magento cache:clean
bin/magento cache:flush
```

## Uninstall

```bash
bin/magento module:uninstall Uala_Bis
```

## License

[MIT](https://choosealicense.com/licenses/mit/)

___
# Screenshots

### Module enabled, name and message configurable:
<img src="https://i.imgur.com/0BOSQ2B.png" alt="" width="600px"/>

### Redirect to Ualá Bis Checkout:
<img src="https://i.imgur.com/e5xCNKf.png" alt="" width="600px"/>

### Redirect to success:
<img src="https://i.imgur.com/0lQq9vY.png" alt="" width="600px"/>

### Redirect to failure:
<img src="https://i.imgur.com/wajxQIJ.png" alt="" width="600px"/>

### Configuration:
<img src="https://i.imgur.com/9gMC4oD.png" alt="" width="800px"/>

### Backend Sales Updated:
<img src="https://i.imgur.com/BSbWcjM.png" alt=""/>

### Order Info:
<img src="https://i.imgur.com/3Uo28Sm.png" alt=""/>

___

## API Checkout Docs

Also, you can show Api Checkout Documentation in https://developers.ualabis.com.ar
