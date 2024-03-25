# Contribution guidelines

Thank you for considering contributing to the project! We welcome all contributions.

## Table of contents

- [Opening an Issue](#opening-an-issue)
- [Submitting a pull request](#submitting-a-pull-request)
- [Coding standards](#coding-standards)

## Opening an issue

Opening an issue is a great way to report bugs and request new features.
If you need help or have a question, prefer to use [Discussions](https://github.com/milceo/milceo/discussions) instead.

> [!WARNING]
> Before opening an issue, always check if there is already an open issue for the same topic. If there is, you can add a
> comment to the existing issue instead of opening a new one. Duplicate issues will be **closed**.

### :bug: Bug reports

If you find a bug in the project, you can [open an issue](https://github.com/milceo/milceo/issues)
and select the **Bug report** template.

Please provide as much information as possible, including steps to reproduce the bug, what you expected to happen, and
what actually happened. You can also include screenshots, videos, or code samples.

If you are able to fix the bug, you can also [submit a pull request](#submitting-a-pull-request).

### :bulb: Feature requests

If you have an idea for a new feature, you can [open an issue](https://github.com/milceo/milceo/issues) and select the
**Feature request** template.

Please provide as much information as possible, including a description of the feature, why you think it would be
useful, and any other context or examples that might be helpful.
You can also include screenshots, videos, or code samples.

If you are able to implement the feature, you can also [submit a pull request](#submitting-a-pull-request).

## Submitting a Pull Request

Before submitting a pull request, please open an issue to discuss the proposed changes.
This will help ensure that your contribution is aligned with the goals of Milceo and that you are not duplicating work
that is already in progress.

### :computer: Clone the repository

First, you will need to clone the repository by running the following command:

```bash
git clone https://github.com/milceo/milceo.git
```

### :sparkles: Create a new branch

Next, create a new branch for your contribution:

```bash
git checkout -b my-new-branch
```

> [!IMPORTANT]
> Please use a descriptive name for your branch, such as `feature/new-feature` for a new feature or `fix/fix-bug` for a
> bug fix.

### :white_check_mark: Run tests

Before submitting your pull request, please run the tests to ensure that your changes do not introduce any new issues.
You are also encouraged to write new tests for your changes.

> [!IMPORTANT]
> We use [PHPUnit](https://phpunit.de/) for testing.
> The library is not listed as a dependency in the `composer.json` file [^1]. To run the tests, you will need to
> use the `phpunit.phar` file that is included in the root directory.

Use the following command to run the tests:

```bash
php phpunit.phar
```

### :pushpin: Commit changes

Once you are happy with your changes, commit them to your branch by running the following command:

```bash
git commit -m "Add new feature"
```

Please use a descriptive commit message that explains what your changes do.

Then, push your changes:

```bash
git push origin my-new-branch
```

### :memo: Update documentation

If your changes affect the project's documentation, please update the [wiki](https://github.com/milceo/milceo/wiki).

### :arrows_counterclockwise: Create a pull request

Finally, [create a pull request](https://github.com/milceo/milceo/pulls).

Select your branch and fill in the details of your pull request, including a description of your changes.

If your pull request fixes an open issue, please reference the issue in your pull request description.

## Coding standards

This project follows the [PSR-12](https://www.php-fig.org/psr/psr-12/) coding standard.
Please ensure that your contributions adhere to this standard.

Please also ensure that your code is well-documented.

[^1]: https://docs.phpunit.de/en/11.0/installation.html#phar-or-composer
