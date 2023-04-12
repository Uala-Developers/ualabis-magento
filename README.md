# Uala_Bis for Magento 2

This Extension is used to make payments using Uala Bis API in Argentina.

- Allow end user set credentials, checkout message and name of payment method 
- Generate pending orders and using return parameters back and change to order status cancel or processing (and create invoice).
- Add validation to prevent unexpected changes on status using urls.
- Can be used in production / test mode just changing credentials.

## Manual Installation

- Create a folder [root]/app/code/Uala/Bis
- Copy to folder
- Check module structure for this files: app/code/Uala/Bis/registration.php and others
- Check module structure for this folder: app/code/Uala/Bis/Model/ app/code/Uala/Bis/Block/ app/code/Uala/Bis/etc/ and others

Then you'll need to activate the module.

```
bin/magento setup:upgrade
bin/magento setup:di:compile
bin/magento cache:clean
bin/magento cache:flush
```

## Uninstall

```
bin/magento module:uninstall Uala_Bis
```

## Support

No warranty or support provided.

## Contributing

Pull requests are welcome. For major changes, please open an issue first to discuss what you would like to change.

Please make sure to update tests as appropriate.

### How to create a PR

1. Fork it
2. Create your feature branch (git checkout -b my-new-feature)
3. Commit your changes (git commit -am 'Add some feature')
4. Push to the branch (git push origin my-new-feature)
5. Create new Pull Request

## License

[MIT](https://choosealicense.com/licenses/mit/)

## Screenshots
<a href="https://imgur.com/0BOSQ2B"><img src="https://i.imgur.com/0BOSQ2B.png"/></a><br>
Module enabled, name and message configurable.<br><br>
<a href="https://imgur.com/9kkmwTO"><img src="https://i.imgur.com/9kkmwTO.png"/></a><br>
Redirect to Uala Bis.<br><br>
<a href="https://imgur.com/z0njCiX"><img src="https://i.imgur.com/z0njCiX.png"/></a><br>
Success payment in Uala Bis.<br><br>
<a href="https://imgur.com/0lQq9vY"><img src="https://i.imgur.com/0lQq9vY.png"/></a><br>
Redirect to success.<br><br>
<a href="https://imgur.com/wajxQIJ"><img src="https://i.imgur.com/wajxQIJ.png"/></a><br>
Redirect to failure.<br><br>
<a href="https://imgur.com/9gMC4oD"><img src="https://i.imgur.com/9gMC4oD.png"/></a><br>
Configuration.<br><br>
<a href="https://imgur.com/BSbWcjM"><img src="https://i.imgur.com/BSbWcjM.png"/></a><br>
Backend Sales Updated.<br><br>
<a href="https://imgur.com/3Uo28Sm"><img src="https://i.imgur.com/3Uo28Sm.png"/></a><br>
Order Info
