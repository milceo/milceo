<p align="center">
  <img src="https://github.com/milceo/assets/blob/main/logo.png" alt="Milceo logo" width=450>
</p>

<p align="center">
    <a href="LICENSE" target="_blank">
        <img alt="Software License" src="https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square">
    </a>
    <a href="https://codecov.io/gh/milceo/milceo"> 
        <img alt="Code coverage" src="https://img.shields.io/codecov/c/github/milceo/milceo?style=flat-square"/> 
    </a>
    <a href="https://packagist.org/packages/cakephp/cakephp" target="_blank">
        <img alt="Latest Stable Version" src="https://img.shields.io/packagist/v/milceo/milceo?style=flat-square&label=stable">
    </a>
</p>

Milceo is a **lightweight** and **fast** framework for PHP 8.3. Milceo is designed to be **simple** and **easy to use**.
If you are familiar with [Symfony](https://github.com/symfony/symfony), you will find that Milceo is very similar in
many ways.

## Table of contents

- [Installation](#installation)
- [Contributing](#contributing)
- [License](#license)

## Installation

### :violin: Install Composer

First, you will need to install [Composer](https://getcomposer.org/) if you haven't already.
You can either download the executable locally or globally.
Please refer to the
[official documentation](https://getcomposer.org/doc/00-intro.md#downloading-the-composer-executable)
for more information.

### :computer: Create a new project

To create a new project, start from the skeleton template by running the following command:

```bash
composer create-project milceo/skeleton app
```

This will create a new directory called `app` with the Milceo framework installed.

### :rocket: Run the application

To run the application, you will need to start the built-in PHP server:

```bash
cd app
php -S localhost:8000 -t public
```

You can then access the application by navigating to http://localhost:8000 in your web browser.

## Contributing

Pull requests are welcome - see the [CONTRIBUTING](CONTRIBUTING.md) file for more information.

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.
