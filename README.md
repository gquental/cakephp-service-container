cakephp-service-container
=========================

This project was created to provide the feature of a container of services and also dependency injection in the CakePHP
framework.

##Installation

In order to install it, there is two options, install it through composer or manually.

###composer

You just need to require the package **gquental/cakephp-service-container** in your **composer.json** and then run the install command. After this process is completed, a folder called **ServiceContainer** will be created inside your CakePHP plugins folder.

###manually

Just clone the project inside your CakePHP plugins folder and name the directory **ServiceContainer**

##Usage

###Configuration file

The first step after the Plugin is properly installed is to create the configuration file and then register it in the CakePHP config bootstrap file **(app/Config/bootstrap.php)**.

For example, if you name the file **services.php**, you will need to write this line in your bootstrap file:

```php
// First parameter is the name of the file without the extension
// The second parameter is the name of the configuration reader,
// which in CakePHP the default is the PHP file reader
Configure::load('services', 'default');
```

####Configuration file example

```php
$config = array(
	'Services' => array(
		'ServiceName' => array(
			'name' => 'name of the class',
			'path' => 'path of the class'
			'arguments' => [1, 2, 3]
		)
	)
);
```

###Using the ServiceContainer Component

In order to call the services inside the container, you will need to use the CakePHP component provided by the Plugin, in order to do so you will need to put the following entry in the components array.

```php
$components = ['ServiceContainer.ServiceContainer'];
```

With the component prepared, you can call a service by calling the **getService** method.

```php
public function actionX() {
	$service = $this->ServiceContainer->getService('serviceName');
}
```

###Dependency injection

Lets say that in your container we have two classes, the User and the Contact class. The contact class needs as a dependency the user instance and also a integer with the maximum number of phones that it should have.

We can pass all of this dependencies through the service container instead of instancing it manually in the Contact class. In order to do so we will to pass arguments to the Contact class in the services container configuration file.

In the arguments we can pass primitive values or another service already in the configuration file, only by passing a string with the name of the service preceded by a **@**, like this for the user class: **"@User"**.

Below you will find the examples of how to create this situation

**app/Config/services.php**

```php
$config = array(
	'Services' => array(
		'User' => array(
			'name' => 'UserEntity',
			'path' => 'Lib'
		),
		'Contact' => array(
			'name' => 'ContactEntity',
			'path' => 'Lib',
			'arguments' => ['@User', 3]
		)
	)
);
```

**someController.php**

```php
public function action() {
	$contact = $this->ServiceContainer->get('Contact');
}
```

**Lib/UserEntity.php**

```php
class UserEntity {}
```

**Lib/ContactEntity.php**

```php
class ContactEntity {
	public $user;
	public $maxPhones;
	
	public function __construct($user, $maxPhones) {
		$this->user = $user;
		$this->maxPhones = $maxPhones;
	}
}
```