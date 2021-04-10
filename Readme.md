# Lestruviens SMS Senders

## About

Lestruviens (ltvsmssenders) is a prestashop module allowing to send sms thanks to the system of AWS..

## Reporting issues

In order to contact the team, please use the link available in the
back-office once logged to your PrestaShop account.

## Building the module

### Direct download

If you want to get a zip ready to install on your shop. You can directly download it by clicking [here][direct-download].

### Production

1. Clone this repo `https://github.com/androbienaime/ltvsmssenders.git`
2. `make build-prod-zip`

The zip will be generated in the root directory of the module.

### Development

1. Clone this repo
2. `make docker-build`
3. `make watch-front`


## Contributing

PrestaShop modules are open source extensions to the PrestaShop e-commerce solution. Everyone is welcome and even encouraged to contribute with their own improvements.

### Requirements

Contributors **must** follow the following rules:

* **Make your Pull Request on the "dev" branch**, NOT the "master" branch.
* Do not update the module's version number.
* Follow [the coding standards][1].

### Process in details

Contributors wishing to edit a module's files should follow the following process:

1. Create your GitHub account, if you do not have one already.
2. Fork this project to your GitHub account.
3. Clone your fork to your local machine in the ```/modules``` directory of your PrestaShop installation.
4. Create a branch in your local clone of the module for your changes.
5. Change the files in your branch. Be sure to follow the [coding standards][1]!
6. Push your changed branch to your fork in your GitHub account.
7. Create a pull request for your changes **on the _'dev'_ branch** of the module's project. Be sure to follow the [contribution guidelines][2] in your pull request. If you need help to make a pull request, read the [GitHub help page about creating pull requests][3].
8. Wait for one of the core developers either to include your change in the codebase, or to comment on possible improvements you should make to your code.

That's it: you have contributed to this open source project! Congratulations!

## License
