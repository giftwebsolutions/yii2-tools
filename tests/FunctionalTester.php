<?php
namespace bariew\yii2Tools\tests;

use Codeception\Module\Filesystem;
use Codeception\Module\Yii2;

/**
 * Inherited Methods
 * @method void wantToTest($text)
 * @method void wantTo($text)
 * @method void execute($callable)
 * @method void expectTo($prediction)
 * @method void expect($prediction)
 * @method void amGoingTo($argumentation)
 * @method void am($role)
 * @method void lookForwardTo($achieveValue)
 * @method void comment($description)
 * @method void haveFriend($name, $actorClass = null)
 *
 * @SuppressWarnings(PHPMD)
*/
class FunctionalTester extends \Codeception\Actor
{
    /**
     * Logs actor in.
     * @param null $username
     * @param null $password
     */
    public function login($username = null, $password = null)
    {
        $this->go('/user/default/login');
        if (!$username) {
            list($username, $password) = array_values(\Yii::$app->params['auth']);
        }
        return $this->submitForm('#login-form', [
            "UserLoginForm[email]" => $username,
            "UserLoginForm[password]" => $password
        ]);
    }

    public function getUrl()
    {
        return str_replace('/'.basename(Yii::$app->request->scriptFile), '', $this->grabFromCurrentUrl());
    }

    public function logout()
    {
        $this->go(['/user/default/logout']);
    }

    public function go($url)
    {
        $this->amGoingTo("Visit " . (is_array($url) ? reset($url) : $url));
        return $this->amOnPage(\Yii::$app->getUrlManager()->createUrl($url));
    }

    /**
     * Sends form post with wrong or/and correct form data
     * and checks whether response has form fields errors.
     * @param $selector
     * @param $name
     * @param $correctData
     * @param array $wrongData
     * @param string $errorSelector
     */
    public function testForm($selector, $name, $correctData, $wrongData = [], $errorSelector = '.has-error')
    {
        $this->testMultipleForm($selector, [$name => ['correct' => $correctData, 'wrong' => $wrongData]], $errorSelector);
    }

    /**
     * @param $selector
     * @param array $formData (['Account' => ['correct' => ['name' => 'asd'], 'wrong'=>['id'=>123]]])
     * @param string $errorSelector
     */
    public function testMultipleForm($selector, $formData, $errorSelector = '.has-error')
    {
        $allData = [];
        foreach ($formData as $name => $data) {
            $allData[$name] = array_merge((array) @$data['correct'], (array) @$data['wrong']);
        }
        $submitData = [];
        foreach ($allData as $name => $data) {
            foreach ($data as $key => $value) {
                $key = explode('[', $key);
                $key[0] = $key[0].']';
                $key = '['.implode('[', $key);
                $submitData["{$name}{$key}"] = $value;
            }
        }
        $this->submitForm($selector, $submitData);
        foreach ($allData as $name => $data) {
            foreach ($data as $key => $value) {
                $key = preg_replace('/.*\[.*/', '$1', $key);
                $selector =
                    $errorSelector
                    . strtolower(".field-"
                        . str_replace(['[', ']'],['-', ''], $name)
                        . "-{$key}"
                    );
                if (isset($formData[$name]['wrong'][$key])) {
                    $this->expectTo("see error message for $key with value $value");
                    $this->seeElement($selector);
                } else {
                    $this->expectTo("NOT to see error message for $key with value $value");
                    $this->dontSeeElement($selector);
                }
            }
        }
    }

    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Enters a directory In local filesystem.
     * Project root directory is used by default
     *
     * @param $path
     * @see \Codeception\Module\Filesystem::amInPath()
     */
    public function amInPath($path) {
        return $this->scenario->runStep(new \Codeception\Step\Condition('amInPath', func_get_args()));
    }

 
    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Opens a file and stores it's content.
     *
     * Usage:
     *
     * ``` php
     * <?php
     * $I->openFile('composer.json');
     * $I->seeInThisFile('codeception/codeception');
     * ?>
     * ```
     *
     * @param $filename
     * @see \Codeception\Module\Filesystem::openFile()
     */
    public function openFile($filename) {
        return $this->scenario->runStep(new \Codeception\Step\Action('openFile', func_get_args()));
    }

 
    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Deletes a file
     *
     * ``` php
     * <?php
     * $I->deleteFile('composer.lock');
     * ?>
     * ```
     *
     * @param $filename
     * @see \Codeception\Module\Filesystem::deleteFile()
     */
    public function deleteFile($filename) {
        return $this->scenario->runStep(new \Codeception\Step\Action('deleteFile', func_get_args()));
    }

 
    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Deletes directory with all subdirectories
     *
     * ``` php
     * <?php
     * $I->deleteDir('vendor');
     * ?>
     * ```
     *
     * @param $dirname
     * @see \Codeception\Module\Filesystem::deleteDir()
     */
    public function deleteDir($dirname) {
        return $this->scenario->runStep(new \Codeception\Step\Action('deleteDir', func_get_args()));
    }

 
    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Copies directory with all contents
     *
     * ``` php
     * <?php
     * $I->copyDir('vendor','old_vendor');
     * ?>
     * ```
     *
     * @param $src
     * @param $dst
     * @see \Codeception\Module\Filesystem::copyDir()
     */
    public function copyDir($src, $dst) {
        return $this->scenario->runStep(new \Codeception\Step\Action('copyDir', func_get_args()));
    }

 
    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Checks If opened file has `text` in it.
     *
     * Usage:
     *
     * ``` php
     * <?php
     * $I->openFile('composer.json');
     * $I->seeInThisFile('codeception/codeception');
     * ?>
     * ```
     *
     * @param $text
     * Conditional Assertion: Test won't be stopped on fail
     * @see \Codeception\Module\Filesystem::seeInThisFile()
     */
    public function canSeeInThisFile($text) {
        return $this->scenario->runStep(new \Codeception\Step\ConditionalAssertion('seeInThisFile', func_get_args()));
    }
    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Checks If opened file has `text` in it.
     *
     * Usage:
     *
     * ``` php
     * <?php
     * $I->openFile('composer.json');
     * $I->seeInThisFile('codeception/codeception');
     * ?>
     * ```
     *
     * @param $text
     * @see \Codeception\Module\Filesystem::seeInThisFile()
     */
    public function seeInThisFile($text) {
        return $this->scenario->runStep(new \Codeception\Step\Assertion('seeInThisFile', func_get_args()));
    }

 
    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Checks the strict matching of file contents.
     * Unlike `seeInThisFile` will fail if file has something more than expected lines.
     * Better to use with HEREDOC strings.
     * Matching is done after removing "\r" chars from file content.
     *
     * ``` php
     * <?php
     * $I->openFile('process.pid');
     * $I->seeFileContentsEqual('3192');
     * ?>
     * ```
     *
     * @param $text
     * Conditional Assertion: Test won't be stopped on fail
     * @see \Codeception\Module\Filesystem::seeFileContentsEqual()
     */
    public function canSeeFileContentsEqual($text) {
        return $this->scenario->runStep(new \Codeception\Step\ConditionalAssertion('seeFileContentsEqual', func_get_args()));
    }
    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Checks the strict matching of file contents.
     * Unlike `seeInThisFile` will fail if file has something more than expected lines.
     * Better to use with HEREDOC strings.
     * Matching is done after removing "\r" chars from file content.
     *
     * ``` php
     * <?php
     * $I->openFile('process.pid');
     * $I->seeFileContentsEqual('3192');
     * ?>
     * ```
     *
     * @param $text
     * @see \Codeception\Module\Filesystem::seeFileContentsEqual()
     */
    public function seeFileContentsEqual($text) {
        return $this->scenario->runStep(new \Codeception\Step\Assertion('seeFileContentsEqual', func_get_args()));
    }

 
    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Checks If opened file doesn't contain `text` in it
     *
     * ``` php
     * <?php
     * $I->openFile('composer.json');
     * $I->dontSeeInThisFile('codeception/codeception');
     * ?>
     * ```
     *
     * @param $text
     * Conditional Assertion: Test won't be stopped on fail
     * @see \Codeception\Module\Filesystem::dontSeeInThisFile()
     */
    public function cantSeeInThisFile($text) {
        return $this->scenario->runStep(new \Codeception\Step\ConditionalAssertion('dontSeeInThisFile', func_get_args()));
    }
    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Checks If opened file doesn't contain `text` in it
     *
     * ``` php
     * <?php
     * $I->openFile('composer.json');
     * $I->dontSeeInThisFile('codeception/codeception');
     * ?>
     * ```
     *
     * @param $text
     * @see \Codeception\Module\Filesystem::dontSeeInThisFile()
     */
    public function dontSeeInThisFile($text) {
        return $this->scenario->runStep(new \Codeception\Step\Assertion('dontSeeInThisFile', func_get_args()));
    }

 
    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Deletes a file
     * @see \Codeception\Module\Filesystem::deleteThisFile()
     */
    public function deleteThisFile() {
        return $this->scenario->runStep(new \Codeception\Step\Action('deleteThisFile', func_get_args()));
    }

 
    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Checks if file exists in path.
     * Opens a file when it's exists
     *
     * ``` php
     * <?php
     * $I->seeFileFound('UserModel.php','app/models');
     * ?>
     * ```
     *
     * @param $filename
     * @param string $path
     * Conditional Assertion: Test won't be stopped on fail
     * @see \Codeception\Module\Filesystem::seeFileFound()
     */
    public function canSeeFileFound($filename, $path = null) {
        return $this->scenario->runStep(new \Codeception\Step\ConditionalAssertion('seeFileFound', func_get_args()));
    }
    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Checks if file exists in path.
     * Opens a file when it's exists
     *
     * ``` php
     * <?php
     * $I->seeFileFound('UserModel.php','app/models');
     * ?>
     * ```
     *
     * @param $filename
     * @param string $path
     * @see \Codeception\Module\Filesystem::seeFileFound()
     */
    public function seeFileFound($filename, $path = null) {
        return $this->scenario->runStep(new \Codeception\Step\Assertion('seeFileFound', func_get_args()));
    }

 
    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Checks if file does not exists in path
     *
     * @param $filename
     * @param string $path
     * Conditional Assertion: Test won't be stopped on fail
     * @see \Codeception\Module\Filesystem::dontSeeFileFound()
     */
    public function cantSeeFileFound($filename, $path = null) {
        return $this->scenario->runStep(new \Codeception\Step\ConditionalAssertion('dontSeeFileFound', func_get_args()));
    }
    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Checks if file does not exists in path
     *
     * @param $filename
     * @param string $path
     * @see \Codeception\Module\Filesystem::dontSeeFileFound()
     */
    public function dontSeeFileFound($filename, $path = null) {
        return $this->scenario->runStep(new \Codeception\Step\Assertion('dontSeeFileFound', func_get_args()));
    }

 
    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Erases directory contents
     *
     * ``` php
     * <?php
     * $I->cleanDir('logs');
     * ?>
     * ```
     *
     * @param $dirname
     * @see \Codeception\Module\Filesystem::cleanDir()
     */
    public function cleanDir($dirname) {
        return $this->scenario->runStep(new \Codeception\Step\Action('cleanDir', func_get_args()));
    }

 
    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Saves contents to file
     *
     * @param $filename
     * @param $contents
     * @see \Codeception\Module\Filesystem::writeToFile()
     */
    public function writeToFile($filename, $contents) {
        return $this->scenario->runStep(new \Codeception\Step\Action('writeToFile', func_get_args()));
    }

 
    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Inserts record into the database.
     *
     * ``` php
     * <?php
     * $user_id = $I->haveRecord('app\models\User', array('name' => 'Davert'));
     * ?>
     * ```
     *
     * @param $model
     * @param array $attributes
     * @return mixed
     * @see \Codeception\Module\Yii2::haveRecord()
     */
    public function haveRecord($model, $attributes = null) {
        return $this->scenario->runStep(new \Codeception\Step\Action('haveRecord', func_get_args()));
    }

 
    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Checks that record exists in database.
     *
     * ``` php
     * $I->seeRecord('app\models\User', array('name' => 'davert'));
     * ```
     *
     * @param $model
     * @param array $attributes
     * Conditional Assertion: Test won't be stopped on fail
     * @see \Codeception\Module\Yii2::seeRecord()
     */
    public function canSeeRecord($model, $attributes = null) {
        return $this->scenario->runStep(new \Codeception\Step\ConditionalAssertion('seeRecord', func_get_args()));
    }
    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Checks that record exists in database.
     *
     * ``` php
     * $I->seeRecord('app\models\User', array('name' => 'davert'));
     * ```
     *
     * @param $model
     * @param array $attributes
     * @see \Codeception\Module\Yii2::seeRecord()
     */
    public function seeRecord($model, $attributes = null) {
        return $this->scenario->runStep(new \Codeception\Step\Assertion('seeRecord', func_get_args()));
    }

 
    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Checks that record does not exist in database.
     *
     * ``` php
     * $I->dontSeeRecord('app\models\User', array('name' => 'davert'));
     * ```
     *
     * @param $model
     * @param array $attributes
     * Conditional Assertion: Test won't be stopped on fail
     * @see \Codeception\Module\Yii2::dontSeeRecord()
     */
    public function cantSeeRecord($model, $attributes = null) {
        return $this->scenario->runStep(new \Codeception\Step\ConditionalAssertion('dontSeeRecord', func_get_args()));
    }
    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Checks that record does not exist in database.
     *
     * ``` php
     * $I->dontSeeRecord('app\models\User', array('name' => 'davert'));
     * ```
     *
     * @param $model
     * @param array $attributes
     * @see \Codeception\Module\Yii2::dontSeeRecord()
     */
    public function dontSeeRecord($model, $attributes = null) {
        return $this->scenario->runStep(new \Codeception\Step\Assertion('dontSeeRecord', func_get_args()));
    }

 
    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Retrieves record from database
     *
     * ``` php
     * $category = $I->grabRecord('app\models\User', array('name' => 'davert'));
     * ```
     *
     * @param $model
     * @param array $attributes
     * @return mixed
     * @see \Codeception\Module\Yii2::grabRecord()
     */
    public function grabRecord($model, $attributes = null) {
        return $this->scenario->runStep(new \Codeception\Step\Action('grabRecord', func_get_args()));
    }

 
    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     *  Converting $page to valid Yii2 url
     *  Allows input like:
     *  $I->amOnPage(['site/view','page'=>'about']);
     *  $I->amOnPage('index-test.php?site/index');
     *  $I->amOnPage('http://localhost/index-test.php?site/index');
     * @see \Codeception\Module\Yii2::amOnPage()
     */
    public function amOnPage($page) {
        return $this->scenario->runStep(new \Codeception\Step\Condition('amOnPage', func_get_args()));
    }

 
    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Authenticates user for HTTP_AUTH
     *
     * @param $username
     * @param $password
     * @see \Codeception\Lib\InnerBrowser::amHttpAuthenticated()
     */
    public function amHttpAuthenticated($username, $password) {
        return $this->scenario->runStep(new \Codeception\Step\Condition('amHttpAuthenticated', func_get_args()));
    }

 
    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Perform a click on a link or a button, given by a locator.
     * If a fuzzy locator is given, the page will be searched for a button, link, or image matching the locator string.
     * For buttons, the "value" attribute, "name" attribute, and inner text are searched.
     * For links, the link text is searched.
     * For images, the "alt" attribute and inner text of any parent links are searched.
     *
     * The second parameter is a context (CSS or XPath locator) to narrow the search.
     *
     * Note that if the locator matches a button of type `submit`, the form will be submitted.
     *
     * ``` php
     * <?php
     * // simple link
     * $I->click('Log Out');
     * // button of form
     * $I->click('Submit');
     * // CSS button
     * $I->click('#form input[type=submit]');
     * // XPath
     * $I->click('//form/*[@type=submit]');
     * // link in context
     * $I->click('Log Out', '#nav');
     * // using strict locator
     * $I->click(['link' => 'Login']);
     * ?>
     * ```
     *
     * @param $link
     * @param $context
     * @see \Codeception\Lib\InnerBrowser::click()
     */
    public function click($link, $context = null) {
        return $this->scenario->runStep(new \Codeception\Step\Action('click', func_get_args()));
    }

 
    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Checks that the current page contains the given string.
     * Specify a locator as the second parameter to match a specific region.
     *
     * ``` php
     * <?php
     * $I->see('Log Out'); // I can suppose user is logged in
     * $I->see('Sign Up','h1'); // I can suppose it's a signup page
     * $I->see('Sign Up','//body/h1'); // with XPath
     * ?>
     * ```
     *
     * @param      $text
     * @param null $selector
     * Conditional Assertion: Test won't be stopped on fail
     * @see \Codeception\Lib\InnerBrowser::see()
     */
    public function canSee($text, $selector = null) {
        return $this->scenario->runStep(new \Codeception\Step\ConditionalAssertion('see', func_get_args()));
    }
    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Checks that the current page contains the given string.
     * Specify a locator as the second parameter to match a specific region.
     *
     * ``` php
     * <?php
     * $I->see('Log Out'); // I can suppose user is logged in
     * $I->see('Sign Up','h1'); // I can suppose it's a signup page
     * $I->see('Sign Up','//body/h1'); // with XPath
     * ?>
     * ```
     *
     * @param      $text
     * @param null $selector
     * @see \Codeception\Lib\InnerBrowser::see()
     */
    public function see($text, $selector = null) {
        return $this->scenario->runStep(new \Codeception\Step\Assertion('see', func_get_args()));
    }

 
    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Checks that the current page doesn't contain the text specified.
     * Give a locator as the second parameter to match a specific region.
     *
     * ```php
     * <?php
     * $I->dontSee('Login'); // I can suppose user is already logged in
     * $I->dontSee('Sign Up','h1'); // I can suppose it's not a signup page
     * $I->dontSee('Sign Up','//body/h1'); // with XPath
     * ?>
     * ```
     *
     * @param      $text
     * @param null $selector
     * Conditional Assertion: Test won't be stopped on fail
     * @see \Codeception\Lib\InnerBrowser::dontSee()
     */
    public function cantSee($text, $selector = null) {
        return $this->scenario->runStep(new \Codeception\Step\ConditionalAssertion('dontSee', func_get_args()));
    }
    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Checks that the current page doesn't contain the text specified.
     * Give a locator as the second parameter to match a specific region.
     *
     * ```php
     * <?php
     * $I->dontSee('Login'); // I can suppose user is already logged in
     * $I->dontSee('Sign Up','h1'); // I can suppose it's not a signup page
     * $I->dontSee('Sign Up','//body/h1'); // with XPath
     * ?>
     * ```
     *
     * @param      $text
     * @param null $selector
     * @see \Codeception\Lib\InnerBrowser::dontSee()
     */
    public function dontSee($text, $selector = null) {
        return $this->scenario->runStep(new \Codeception\Step\Assertion('dontSee', func_get_args()));
    }

 
    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Checks that there's a link with the specified text.
     * Give a full URL as the second parameter to match links with that exact URL.
     *
     * ``` php
     * <?php
     * $I->seeLink('Log Out'); // matches <a href="#">Log Out</a>
     * $I->seeLink('Log Out','/logout'); // matches <a href="<?= Yii::$app->urlManager->createUrl(['/logout']) ?>">Log Out</a>
     * ?>
     * ```
     *
     * @param      $text
     * @param null $url
     * Conditional Assertion: Test won't be stopped on fail
     * @see \Codeception\Lib\InnerBrowser::seeLink()
     */
    public function canSeeLink($text, $url = null) {
        return $this->scenario->runStep(new \Codeception\Step\ConditionalAssertion('seeLink', func_get_args()));
    }
    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Checks that there's a link with the specified text.
     * Give a full URL as the second parameter to match links with that exact URL.
     *
     * ``` php
     * <?php
     * $I->seeLink('Log Out'); // matches <a href="#">Log Out</a>
     * $I->seeLink('Log Out','/logout'); // matches <a href="<?= Yii::$app->urlManager->createUrl(['/logout']) ?>">Log Out</a>
     * ?>
     * ```
     *
     * @param      $text
     * @param null $url
     * @see \Codeception\Lib\InnerBrowser::seeLink()
     */
    public function seeLink($text, $url = null) {
        return $this->scenario->runStep(new \Codeception\Step\Assertion('seeLink', func_get_args()));
    }

 
    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Checks that the page doesn't contain a link with the given string.
     * If the second parameter is given, only links with a matching "href" attribute will be checked.
     *
     * ``` php
     * <?php
     * $I->dontSeeLink('Log Out'); // I suppose user is not logged in
     * $I->dontSeeLink('Checkout now', '/store/cart.php');
     * ?>
     * ```
     *
     * @param $text
     * @param null $url
     * Conditional Assertion: Test won't be stopped on fail
     * @see \Codeception\Lib\InnerBrowser::dontSeeLink()
     */
    public function cantSeeLink($text, $url = null) {
        return $this->scenario->runStep(new \Codeception\Step\ConditionalAssertion('dontSeeLink', func_get_args()));
    }
    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Checks that the page doesn't contain a link with the given string.
     * If the second parameter is given, only links with a matching "href" attribute will be checked.
     *
     * ``` php
     * <?php
     * $I->dontSeeLink('Log Out'); // I suppose user is not logged in
     * $I->dontSeeLink('Checkout now', '/store/cart.php');
     * ?>
     * ```
     *
     * @param $text
     * @param null $url
     * @see \Codeception\Lib\InnerBrowser::dontSeeLink()
     */
    public function dontSeeLink($text, $url = null) {
        return $this->scenario->runStep(new \Codeception\Step\Assertion('dontSeeLink', func_get_args()));
    }

 
    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Checks that current URI contains the given string.
     *
     * ``` php
     * <?php
     * // to match: /home/dashboard
     * $I->seeInCurrentUrl('home');
     * // to match: /users/1
     * $I->seeInCurrentUrl('/users/');
     * ?>
     * ```
     *
     * @param $uri
     * Conditional Assertion: Test won't be stopped on fail
     * @see \Codeception\Lib\InnerBrowser::seeInCurrentUrl()
     */
    public function canSeeInCurrentUrl($uri) {
        return $this->scenario->runStep(new \Codeception\Step\ConditionalAssertion('seeInCurrentUrl', func_get_args()));
    }
    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Checks that current URI contains the given string.
     *
     * ``` php
     * <?php
     * // to match: /home/dashboard
     * $I->seeInCurrentUrl('home');
     * // to match: /users/1
     * $I->seeInCurrentUrl('/users/');
     * ?>
     * ```
     *
     * @param $uri
     * @see \Codeception\Lib\InnerBrowser::seeInCurrentUrl()
     */
    public function seeInCurrentUrl($uri) {
        return $this->scenario->runStep(new \Codeception\Step\Assertion('seeInCurrentUrl', func_get_args()));
    }

 
    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Checks that the current URI doesn't contain the given string.
     *
     * ``` php
     * <?php
     * $I->dontSeeInCurrentUrl('/users/');
     * ?>
     * ```
     *
     * @param $uri
     * Conditional Assertion: Test won't be stopped on fail
     * @see \Codeception\Lib\InnerBrowser::dontSeeInCurrentUrl()
     */
    public function cantSeeInCurrentUrl($uri) {
        return $this->scenario->runStep(new \Codeception\Step\ConditionalAssertion('dontSeeInCurrentUrl', func_get_args()));
    }
    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Checks that the current URI doesn't contain the given string.
     *
     * ``` php
     * <?php
     * $I->dontSeeInCurrentUrl('/users/');
     * ?>
     * ```
     *
     * @param $uri
     * @see \Codeception\Lib\InnerBrowser::dontSeeInCurrentUrl()
     */
    public function dontSeeInCurrentUrl($uri) {
        return $this->scenario->runStep(new \Codeception\Step\Assertion('dontSeeInCurrentUrl', func_get_args()));
    }

 
    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Checks that the current URL is equal to the given string.
     * Unlike `seeInCurrentUrl`, this only matches the full URL.
     *
     * ``` php
     * <?php
     * // to match root url
     * $I->seeCurrentUrlEquals('/');
     * ?>
     * ```
     *
     * @param $uri
     * Conditional Assertion: Test won't be stopped on fail
     * @see \Codeception\Lib\InnerBrowser::seeCurrentUrlEquals()
     */
    public function canSeeCurrentUrlEquals($uri) {
        return $this->scenario->runStep(new \Codeception\Step\ConditionalAssertion('seeCurrentUrlEquals', func_get_args()));
    }
    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Checks that the current URL is equal to the given string.
     * Unlike `seeInCurrentUrl`, this only matches the full URL.
     *
     * ``` php
     * <?php
     * // to match root url
     * $I->seeCurrentUrlEquals('/');
     * ?>
     * ```
     *
     * @param $uri
     * @see \Codeception\Lib\InnerBrowser::seeCurrentUrlEquals()
     */
    public function seeCurrentUrlEquals($uri) {
        return $this->scenario->runStep(new \Codeception\Step\Assertion('seeCurrentUrlEquals', func_get_args()));
    }

 
    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Checks that the current URL doesn't equal the given string.
     * Unlike `dontSeeInCurrentUrl`, this only matches the full URL.
     *
     * ``` php
     * <?php
     * // current url is not root
     * $I->dontSeeCurrentUrlEquals('/');
     * ?>
     * ```
     *
     * @param $uri
     * Conditional Assertion: Test won't be stopped on fail
     * @see \Codeception\Lib\InnerBrowser::dontSeeCurrentUrlEquals()
     */
    public function cantSeeCurrentUrlEquals($uri) {
        return $this->scenario->runStep(new \Codeception\Step\ConditionalAssertion('dontSeeCurrentUrlEquals', func_get_args()));
    }
    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Checks that the current URL doesn't equal the given string.
     * Unlike `dontSeeInCurrentUrl`, this only matches the full URL.
     *
     * ``` php
     * <?php
     * // current url is not root
     * $I->dontSeeCurrentUrlEquals('/');
     * ?>
     * ```
     *
     * @param $uri
     * @see \Codeception\Lib\InnerBrowser::dontSeeCurrentUrlEquals()
     */
    public function dontSeeCurrentUrlEquals($uri) {
        return $this->scenario->runStep(new \Codeception\Step\Assertion('dontSeeCurrentUrlEquals', func_get_args()));
    }

 
    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Checks that the current URL matches the given regular expression.
     *
     * ``` php
     * <?php
     * // to match root url
     * $I->seeCurrentUrlMatches('~$/users/(\d+)~');
     * ?>
     * ```
     *
     * @param $uri
     * Conditional Assertion: Test won't be stopped on fail
     * @see \Codeception\Lib\InnerBrowser::seeCurrentUrlMatches()
     */
    public function canSeeCurrentUrlMatches($uri) {
        return $this->scenario->runStep(new \Codeception\Step\ConditionalAssertion('seeCurrentUrlMatches', func_get_args()));
    }
    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Checks that the current URL matches the given regular expression.
     *
     * ``` php
     * <?php
     * // to match root url
     * $I->seeCurrentUrlMatches('~$/users/(\d+)~');
     * ?>
     * ```
     *
     * @param $uri
     * @see \Codeception\Lib\InnerBrowser::seeCurrentUrlMatches()
     */
    public function seeCurrentUrlMatches($uri) {
        return $this->scenario->runStep(new \Codeception\Step\Assertion('seeCurrentUrlMatches', func_get_args()));
    }

 
    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Checks that current url doesn't match the given regular expression.
     *
     * ``` php
     * <?php
     * // to match root url
     * $I->dontSeeCurrentUrlMatches('~$/users/(\d+)~');
     * ?>
     * ```
     *
     * @param $uri
     * Conditional Assertion: Test won't be stopped on fail
     * @see \Codeception\Lib\InnerBrowser::dontSeeCurrentUrlMatches()
     */
    public function cantSeeCurrentUrlMatches($uri) {
        return $this->scenario->runStep(new \Codeception\Step\ConditionalAssertion('dontSeeCurrentUrlMatches', func_get_args()));
    }
    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Checks that current url doesn't match the given regular expression.
     *
     * ``` php
     * <?php
     * // to match root url
     * $I->dontSeeCurrentUrlMatches('~$/users/(\d+)~');
     * ?>
     * ```
     *
     * @param $uri
     * @see \Codeception\Lib\InnerBrowser::dontSeeCurrentUrlMatches()
     */
    public function dontSeeCurrentUrlMatches($uri) {
        return $this->scenario->runStep(new \Codeception\Step\Assertion('dontSeeCurrentUrlMatches', func_get_args()));
    }

 
    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Executes the given regular expression against the current URI and returns the first match.
     * If no parameters are provided, the full URI is returned.
     *
     * ``` php
     * <?php
     * $user_id = $I->grabFromCurrentUrl('~$/user/(\d+)/~');
     * $uri = $I->grabFromCurrentUrl();
     * ?>
     * ```
     *
     * @param null $uri
     *
     * @internal param $url
     * @return mixed
     * @see \Codeception\Lib\InnerBrowser::grabFromCurrentUrl()
     */
    public function grabFromCurrentUrl($uri = null) {
        return $this->scenario->runStep(new \Codeception\Step\Action('grabFromCurrentUrl', func_get_args()));
    }

 
    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Checks that the specified checkbox is checked.
     *
     * ``` php
     * <?php
     * $I->seeCheckboxIsChecked('#agree'); // I suppose user agreed to terms
     * $I->seeCheckboxIsChecked('#signup_form input[type=checkbox]'); // I suppose user agreed to terms, If there is only one checkbox in form.
     * $I->seeCheckboxIsChecked('//form/input[@type=checkbox and @name=agree]');
     * ?>
     * ```
     *
     * @param $checkbox
     * Conditional Assertion: Test won't be stopped on fail
     * @see \Codeception\Lib\InnerBrowser::seeCheckboxIsChecked()
     */
    public function canSeeCheckboxIsChecked($checkbox) {
        return $this->scenario->runStep(new \Codeception\Step\ConditionalAssertion('seeCheckboxIsChecked', func_get_args()));
    }
    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Checks that the specified checkbox is checked.
     *
     * ``` php
     * <?php
     * $I->seeCheckboxIsChecked('#agree'); // I suppose user agreed to terms
     * $I->seeCheckboxIsChecked('#signup_form input[type=checkbox]'); // I suppose user agreed to terms, If there is only one checkbox in form.
     * $I->seeCheckboxIsChecked('//form/input[@type=checkbox and @name=agree]');
     * ?>
     * ```
     *
     * @param $checkbox
     * @see \Codeception\Lib\InnerBrowser::seeCheckboxIsChecked()
     */
    public function seeCheckboxIsChecked($checkbox) {
        return $this->scenario->runStep(new \Codeception\Step\Assertion('seeCheckboxIsChecked', func_get_args()));
    }

 
    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Check that the specified checkbox is unchecked.
     *
     * ``` php
     * <?php
     * $I->dontSeeCheckboxIsChecked('#agree'); // I suppose user didn't agree to terms
     * $I->seeCheckboxIsChecked('#signup_form input[type=checkbox]'); // I suppose user didn't check the first checkbox in form.
     * ?>
     * ```
     *
     * @param $checkbox
     * Conditional Assertion: Test won't be stopped on fail
     * @see \Codeception\Lib\InnerBrowser::dontSeeCheckboxIsChecked()
     */
    public function cantSeeCheckboxIsChecked($checkbox) {
        return $this->scenario->runStep(new \Codeception\Step\ConditionalAssertion('dontSeeCheckboxIsChecked', func_get_args()));
    }
    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Check that the specified checkbox is unchecked.
     *
     * ``` php
     * <?php
     * $I->dontSeeCheckboxIsChecked('#agree'); // I suppose user didn't agree to terms
     * $I->seeCheckboxIsChecked('#signup_form input[type=checkbox]'); // I suppose user didn't check the first checkbox in form.
     * ?>
     * ```
     *
     * @param $checkbox
     * @see \Codeception\Lib\InnerBrowser::dontSeeCheckboxIsChecked()
     */
    public function dontSeeCheckboxIsChecked($checkbox) {
        return $this->scenario->runStep(new \Codeception\Step\Assertion('dontSeeCheckboxIsChecked', func_get_args()));
    }

 
    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Checks that the given input field or textarea contains the given value. 
     * For fuzzy locators, fields are matched by label text, the "name" attribute, CSS, and XPath.
     *
     * ``` php
     * <?php
     * $I->seeInField('Body','Type your comment here');
     * $I->seeInField('form textarea[name=body]','Type your comment here');
     * $I->seeInField('form input[type=hidden]','hidden_value');
     * $I->seeInField('#searchform input','Search');
     * $I->seeInField('//form/*[@name=search]','Search');
     * $I->seeInField(['name' => 'search'], 'Search');
     * ?>
     * ```
     *
     * @param $field
     * @param $value
     * Conditional Assertion: Test won't be stopped on fail
     * @see \Codeception\Lib\InnerBrowser::seeInField()
     */
    public function canSeeInField($field, $value) {
        return $this->scenario->runStep(new \Codeception\Step\ConditionalAssertion('seeInField', func_get_args()));
    }
    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Checks that the given input field or textarea contains the given value. 
     * For fuzzy locators, fields are matched by label text, the "name" attribute, CSS, and XPath.
     *
     * ``` php
     * <?php
     * $I->seeInField('Body','Type your comment here');
     * $I->seeInField('form textarea[name=body]','Type your comment here');
     * $I->seeInField('form input[type=hidden]','hidden_value');
     * $I->seeInField('#searchform input','Search');
     * $I->seeInField('//form/*[@name=search]','Search');
     * $I->seeInField(['name' => 'search'], 'Search');
     * ?>
     * ```
     *
     * @param $field
     * @param $value
     * @see \Codeception\Lib\InnerBrowser::seeInField()
     */
    public function seeInField($field, $value) {
        return $this->scenario->runStep(new \Codeception\Step\Assertion('seeInField', func_get_args()));
    }

 
    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Checks that an input field or textarea doesn't contain the given value.
     * For fuzzy locators, the field is matched by label text, CSS and XPath.
     *
     * ``` php
     * <?php
     * $I->dontSeeInField('Body','Type your comment here');
     * $I->dontSeeInField('form textarea[name=body]','Type your comment here');
     * $I->dontSeeInField('form input[type=hidden]','hidden_value');
     * $I->dontSeeInField('#searchform input','Search');
     * $I->dontSeeInField('//form/*[@name=search]','Search');
     * $I->dontSeeInField(['name' => 'search'], 'Search');
     * ?>
     * ```
     *
     * @param $field
     * @param $value
     * Conditional Assertion: Test won't be stopped on fail
     * @see \Codeception\Lib\InnerBrowser::dontSeeInField()
     */
    public function cantSeeInField($field, $value) {
        return $this->scenario->runStep(new \Codeception\Step\ConditionalAssertion('dontSeeInField', func_get_args()));
    }
    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Checks that an input field or textarea doesn't contain the given value.
     * For fuzzy locators, the field is matched by label text, CSS and XPath.
     *
     * ``` php
     * <?php
     * $I->dontSeeInField('Body','Type your comment here');
     * $I->dontSeeInField('form textarea[name=body]','Type your comment here');
     * $I->dontSeeInField('form input[type=hidden]','hidden_value');
     * $I->dontSeeInField('#searchform input','Search');
     * $I->dontSeeInField('//form/*[@name=search]','Search');
     * $I->dontSeeInField(['name' => 'search'], 'Search');
     * ?>
     * ```
     *
     * @param $field
     * @param $value
     * @see \Codeception\Lib\InnerBrowser::dontSeeInField()
     */
    public function dontSeeInField($field, $value) {
        return $this->scenario->runStep(new \Codeception\Step\Assertion('dontSeeInField', func_get_args()));
    }

 
    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Submits the given form on the page, optionally with the given form values.
     * Give the form fields values as an array.
     *
     * Skipped fields will be filled by their values from the page.
     * You don't need to click the 'Submit' button afterwards.
     * This command itself triggers the request to form's action.
     *
     * You can optionally specify what button's value to include
     * in the request with the last parameter as an alternative to
     * explicitly setting its value in the second parameter, as
     * button values are not otherwise included in the request.
     * 
     * Examples:
     *
     * ``` php
     * <?php
     * $I->submitForm('#login', array('login' => 'davert', 'password' => '123456'));
     * // or
     * $I->submitForm('#login', array('login' => 'davert', 'password' => '123456'), 'submitButtonName');
     *
     * ```
     *
     * For example, given this sample "Sign Up" form:
     *
     * ``` html
     * <form action="<?= Yii::$app->urlManager->createUrl(['/sign_up']) ?>">
     *     Login: <input type="text" name="user[login]" /><br/>
     *     Password: <input type="password" name="user[password]" /><br/>
     *     Do you agree to out terms? <input type="checkbox" name="user[agree]" /><br/>
     *     Select pricing plan <select name="plan"><option value="1">Free</option><option value="2" selected="selected">Paid</option></select>
     *     <input type="submit" name="submitButton" value="Submit" />
     * </form>
     * ```
     *
     * You could write the following to submit it:
     *
     * ``` php
     * <?php
     * $I->submitForm('#userForm', array('user' => array('login' => 'Davert', 'password' => '123456', 'agree' => true)), 'submitButton');
     *
     * ```
     * Note that "2" will be the submitted value for the "plan" field, as it is the selected option.
     * 
     * You can also emulate a JavaScript submission by not specifying any buttons in the third parameter to submitForm.
     * 
     * ```php
     * <?php
     * $I->submitForm('#userForm', array('user' => array('login' => 'Davert', 'password' => '123456', 'agree' => true)));
     * 
     * ```
     *
     * @param $selector
     * @param $params
     * @param $button
     * @see \Codeception\Lib\InnerBrowser::submitForm()
     */
    public function submitForm($selector, $params, $button = null) {
        return $this->scenario->runStep(new \Codeception\Step\Action('submitForm', func_get_args()));
    }

 
    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Fills a text field or textarea with the given string.
     *
     * ``` php
     * <?php
     * $I->fillField("//input[@type='text']", "Hello World!");
     * $I->fillField(['name' => 'email'], 'jon@mail.com');
     * ?>
     * ```
     *
     * @param $field
     * @param $value
     * @see \Codeception\Lib\InnerBrowser::fillField()
     */
    public function fillField($field, $value) {
        return $this->scenario->runStep(new \Codeception\Step\Action('fillField', func_get_args()));
    }

 
    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Selects an option in a select tag or in radio button group.
     *
     * ``` php
     * <?php
     * $I->selectOption('form select[name=account]', 'Premium');
     * $I->selectOption('form input[name=payment]', 'Monthly');
     * $I->selectOption('//form/select[@name=account]', 'Monthly');
     * ?>
     * ```
     *
     * Provide an array for the second argument to select multiple options:
     *
     * ``` php
     * <?php
     * $I->selectOption('Which OS do you use?', array('Windows','Linux'));
     * ?>
     * ```
     *
     * @param $select
     * @param $option
     * @see \Codeception\Lib\InnerBrowser::selectOption()
     */
    public function selectOption($select, $option) {
        return $this->scenario->runStep(new \Codeception\Step\Action('selectOption', func_get_args()));
    }

 
    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Ticks a checkbox. For radio buttons, use the `selectOption` method instead.
     *
     * ``` php
     * <?php
     * $I->checkOption('#agree');
     * ?>
     * ```
     *
     * @param $option
     * @see \Codeception\Lib\InnerBrowser::checkOption()
     */
    public function checkOption($option) {
        return $this->scenario->runStep(new \Codeception\Step\Action('checkOption', func_get_args()));
    }

 
    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Unticks a checkbox.
     *
     * ``` php
     * <?php
     * $I->uncheckOption('#notify');
     * ?>
     * ```
     *
     * @param $option
     * @see \Codeception\Lib\InnerBrowser::uncheckOption()
     */
    public function uncheckOption($option) {
        return $this->scenario->runStep(new \Codeception\Step\Action('uncheckOption', func_get_args()));
    }

 
    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Attaches a file relative to the Codeception data directory to the given file upload field.
     *
     * ``` php
     * <?php
     * // file is stored in 'tests/_data/prices.xls'
     * $I->attachFile('input[@type="file"]', 'prices.xls');
     * ?>
     * ```
     *
     * @param $field
     * @param $filename
     * @see \Codeception\Lib\InnerBrowser::attachFile()
     */
    public function attachFile($field, $filename) {
        return $this->scenario->runStep(new \Codeception\Step\Action('attachFile', func_get_args()));
    }

 
    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * If your page triggers an ajax request, you can perform it manually.
     * This action sends a GET ajax request with specified params.
     *
     * See ->sendAjaxPostRequest for examples.
     *
     * @param $uri
     * @param $params
     * @see \Codeception\Lib\InnerBrowser::sendAjaxGetRequest()
     */
    public function sendAjaxGetRequest($uri, $params = null) {
        return $this->scenario->runStep(new \Codeception\Step\Action('sendAjaxGetRequest', func_get_args()));
    }

 
    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * If your page triggers an ajax request, you can perform it manually.
     * This action sends a POST ajax request with specified params.
     * Additional params can be passed as array.
     *
     * Example:
     *
     * Imagine that by clicking checkbox you trigger ajax request which updates user settings.
     * We emulate that click by running this ajax request manually.
     *
     * ``` php
     * <?php
     * $I->sendAjaxPostRequest('/updateSettings', array('notifications' => true)); // POST
     * $I->sendAjaxGetRequest('/updateSettings', array('notifications' => true)); // GET
     *
     * ```
     *
     * @param $uri
     * @param $params
     * @see \Codeception\Lib\InnerBrowser::sendAjaxPostRequest()
     */
    public function sendAjaxPostRequest($uri, $params = null) {
        return $this->scenario->runStep(new \Codeception\Step\Action('sendAjaxPostRequest', func_get_args()));
    }

 
    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * If your page triggers an ajax request, you can perform it manually.
     * This action sends an ajax request with specified method and params.
     *
     * Example:
     *
     * You need to perform an ajax request specifying the HTTP method.
     *
     * ``` php
     * <?php
     * $I->sendAjaxRequest('PUT', '/posts/7', array('title' => 'new title'));
     *
     * ```
     *
     * @param $method
     * @param $uri
     * @param $params
     * @see \Codeception\Lib\InnerBrowser::sendAjaxRequest()
     */
    public function sendAjaxRequest($method, $uri, $params = null) {
        return $this->scenario->runStep(new \Codeception\Step\Action('sendAjaxRequest', func_get_args()));
    }

 
    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Finds and returns the text contents of the given element.
     * If a fuzzy locator is used, the element is found using CSS, XPath, and by matching the full page source by regular expression.
     *
     * ``` php
     * <?php
     * $heading = $I->grabTextFrom('h1');
     * $heading = $I->grabTextFrom('descendant-or-self::h1');
     * $value = $I->grabTextFrom('~<input value=(.*?)]~sgi'); // match with a regex
     * ?>
     * ```
     *
     * @param $cssOrXPathOrRegex
     *
     * @return mixed
     * @see \Codeception\Lib\InnerBrowser::grabTextFrom()
     */
    public function grabTextFrom($cssOrXPathOrRegex) {
        return $this->scenario->runStep(new \Codeception\Step\Action('grabTextFrom', func_get_args()));
    }

 
    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Grabs the value of the given attribute value from the given element.
     * Fails if element is not found.
     *
     * ``` php
     * <?php
     * $I->grabAttributeFrom('#tooltip', 'title');
     * ?>
     * ```
     *
     *
     * @param $cssOrXpath
     * @param $attribute
     * @internal param $element
     * @return mixed
     * @see \Codeception\Lib\InnerBrowser::grabAttributeFrom()
     */
    public function grabAttributeFrom($cssOrXpath, $attribute) {
        return $this->scenario->runStep(new \Codeception\Step\Action('grabAttributeFrom', func_get_args()));
    }

 
    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * @param $field
     *
     * @return array|mixed|null|string
     * @see \Codeception\Lib\InnerBrowser::grabValueFrom()
     */
    public function grabValueFrom($field) {
        return $this->scenario->runStep(new \Codeception\Step\Action('grabValueFrom', func_get_args()));
    }

 
    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Sets a cookie with the given name and value.
     *
     * ``` php
     * <?php
     * $I->setCookie('PHPSESSID', 'el4ukv0kqbvoirg7nkp4dncpk3');
     * ?>
     * ```
     *
     * @param $cookie
     * @param $value
     *
     * @return mixed
     * @see \Codeception\Lib\InnerBrowser::setCookie()
     */
    public function setCookie($name, $val) {
        return $this->scenario->runStep(new \Codeception\Step\Action('setCookie', func_get_args()));
    }

 
    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Grabs a cookie value.
     *
     * @param $cookie
     *
     * @return mixed
     * @see \Codeception\Lib\InnerBrowser::grabCookie()
     */
    public function grabCookie($name) {
        return $this->scenario->runStep(new \Codeception\Step\Action('grabCookie', func_get_args()));
    }

 
    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Checks that a cookie with the given name is set.
     *
     * ``` php
     * <?php
     * $I->seeCookie('PHPSESSID');
     * ?>
     * ```
     *
     * @param $cookie
     *
     * @return mixed
     * Conditional Assertion: Test won't be stopped on fail
     * @see \Codeception\Lib\InnerBrowser::seeCookie()
     */
    public function canSeeCookie($name) {
        return $this->scenario->runStep(new \Codeception\Step\ConditionalAssertion('seeCookie', func_get_args()));
    }
    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Checks that a cookie with the given name is set.
     *
     * ``` php
     * <?php
     * $I->seeCookie('PHPSESSID');
     * ?>
     * ```
     *
     * @param $cookie
     *
     * @return mixed
     * @see \Codeception\Lib\InnerBrowser::seeCookie()
     */
    public function seeCookie($name) {
        return $this->scenario->runStep(new \Codeception\Step\Assertion('seeCookie', func_get_args()));
    }

 
    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Checks that there isn't a cookie with the given name.
     *
     * @param $cookie
     *
     * @return mixed
     * Conditional Assertion: Test won't be stopped on fail
     * @see \Codeception\Lib\InnerBrowser::dontSeeCookie()
     */
    public function cantSeeCookie($name) {
        return $this->scenario->runStep(new \Codeception\Step\ConditionalAssertion('dontSeeCookie', func_get_args()));
    }
    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Checks that there isn't a cookie with the given name.
     *
     * @param $cookie
     *
     * @return mixed
     * @see \Codeception\Lib\InnerBrowser::dontSeeCookie()
     */
    public function dontSeeCookie($name) {
        return $this->scenario->runStep(new \Codeception\Step\Assertion('dontSeeCookie', func_get_args()));
    }

 
    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Unsets cookie with the given name.
     *
     * @param $cookie
     *
     * @return mixed
     * @see \Codeception\Lib\InnerBrowser::resetCookie()
     */
    public function resetCookie($name) {
        return $this->scenario->runStep(new \Codeception\Step\Action('resetCookie', func_get_args()));
    }

 
    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Checks that the given element exists on the page and is visible.
     * You can also specify expected attributes of this element.
     *
     * ``` php
     * <?php
     * $I->seeElement('.error');
     * $I->seeElement('//form/input[1]');
     * $I->seeElement('input', ['name' => 'login']);
     * $I->seeElement('input', ['value' => '123456']);
     *
     * // strict locator in first arg, attributes in second
     * $I->seeElement(['css' => 'form input'], ['name' => 'login']);
     * ?>
     * ```
     *
     * @param $selector
     * @param array $attributes
     * @return
     * Conditional Assertion: Test won't be stopped on fail
     * @see \Codeception\Lib\InnerBrowser::seeElement()
     */
    public function canSeeElement($selector, $attributes = null) {
        return $this->scenario->runStep(new \Codeception\Step\ConditionalAssertion('seeElement', func_get_args()));
    }
    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Checks that the given element exists on the page and is visible.
     * You can also specify expected attributes of this element.
     *
     * ``` php
     * <?php
     * $I->seeElement('.error');
     * $I->seeElement('//form/input[1]');
     * $I->seeElement('input', ['name' => 'login']);
     * $I->seeElement('input', ['value' => '123456']);
     *
     * // strict locator in first arg, attributes in second
     * $I->seeElement(['css' => 'form input'], ['name' => 'login']);
     * ?>
     * ```
     *
     * @param $selector
     * @param array $attributes
     * @return
     * @see \Codeception\Lib\InnerBrowser::seeElement()
     */
    public function seeElement($selector, $attributes = null) {
        return $this->scenario->runStep(new \Codeception\Step\Assertion('seeElement', func_get_args()));
    }

 
    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Checks that the given element is invisible or not present on the page.
     * You can also specify expected attributes of this element.
     *
     * ``` php
     * <?php
     * $I->dontSeeElement('.error');
     * $I->dontSeeElement('//form/input[1]');
     * $I->dontSeeElement('input', ['name' => 'login']);
     * $I->dontSeeElement('input', ['value' => '123456']);
     * ?>
     * ```
     *
     * @param $selector
     * @param array $attributes
     * Conditional Assertion: Test won't be stopped on fail
     * @see \Codeception\Lib\InnerBrowser::dontSeeElement()
     */
    public function cantSeeElement($selector, $attributes = null) {
        return $this->scenario->runStep(new \Codeception\Step\ConditionalAssertion('dontSeeElement', func_get_args()));
    }
    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Checks that the given element is invisible or not present on the page.
     * You can also specify expected attributes of this element.
     *
     * ``` php
     * <?php
     * $I->dontSeeElement('.error');
     * $I->dontSeeElement('//form/input[1]');
     * $I->dontSeeElement('input', ['name' => 'login']);
     * $I->dontSeeElement('input', ['value' => '123456']);
     * ?>
     * ```
     *
     * @param $selector
     * @param array $attributes
     * @see \Codeception\Lib\InnerBrowser::dontSeeElement()
     */
    public function dontSeeElement($selector, $attributes = null) {
        return $this->scenario->runStep(new \Codeception\Step\Assertion('dontSeeElement', func_get_args()));
    }

 
    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Checks that there are a certain number of elements matched by the given locator on the page.
     * 
     * ``` php
     * <?php
     * $I->seeNumberOfElements('tr', 10);
     * $I->seeNumberOfElements('tr', [0,10]); //between 0 and 10 elements
     * ?>
     * ```
     * @param $selector
     * @param mixed $expected:
     * - string: strict number
     * - array: range of numbers [0,10]
     * Conditional Assertion: Test won't be stopped on fail
     * @see \Codeception\Lib\InnerBrowser::seeNumberOfElements()
     */
    public function canSeeNumberOfElements($selector, $expected) {
        return $this->scenario->runStep(new \Codeception\Step\ConditionalAssertion('seeNumberOfElements', func_get_args()));
    }
    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Checks that there are a certain number of elements matched by the given locator on the page.
     * 
     * ``` php
     * <?php
     * $I->seeNumberOfElements('tr', 10);
     * $I->seeNumberOfElements('tr', [0,10]); //between 0 and 10 elements
     * ?>
     * ```
     * @param $selector
     * @param mixed $expected:
     * - string: strict number
     * - array: range of numbers [0,10]
     * @see \Codeception\Lib\InnerBrowser::seeNumberOfElements()
     */
    public function seeNumberOfElements($selector, $expected) {
        return $this->scenario->runStep(new \Codeception\Step\Assertion('seeNumberOfElements', func_get_args()));
    }

 
    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Checks that the given option is selected.
     *
     * ``` php
     * <?php
     * $I->seeOptionIsSelected('#form input[name=payment]', 'Visa');
     * ?>
     * ```
     *
     * @param $selector
     * @param $optionText
     *
     * @return mixed
     * Conditional Assertion: Test won't be stopped on fail
     * @see \Codeception\Lib\InnerBrowser::seeOptionIsSelected()
     */
    public function canSeeOptionIsSelected($select, $optionText) {
        return $this->scenario->runStep(new \Codeception\Step\ConditionalAssertion('seeOptionIsSelected', func_get_args()));
    }
    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Checks that the given option is selected.
     *
     * ``` php
     * <?php
     * $I->seeOptionIsSelected('#form input[name=payment]', 'Visa');
     * ?>
     * ```
     *
     * @param $selector
     * @param $optionText
     *
     * @return mixed
     * @see \Codeception\Lib\InnerBrowser::seeOptionIsSelected()
     */
    public function seeOptionIsSelected($select, $optionText) {
        return $this->scenario->runStep(new \Codeception\Step\Assertion('seeOptionIsSelected', func_get_args()));
    }

 
    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Checks that the given option is not selected.
     *
     * ``` php
     * <?php
     * $I->dontSeeOptionIsSelected('#form input[name=payment]', 'Visa');
     * ?>
     * ```
     *
     * @param $selector
     * @param $optionText
     *
     * @return mixed
     * Conditional Assertion: Test won't be stopped on fail
     * @see \Codeception\Lib\InnerBrowser::dontSeeOptionIsSelected()
     */
    public function cantSeeOptionIsSelected($select, $optionText) {
        return $this->scenario->runStep(new \Codeception\Step\ConditionalAssertion('dontSeeOptionIsSelected', func_get_args()));
    }
    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Checks that the given option is not selected.
     *
     * ``` php
     * <?php
     * $I->dontSeeOptionIsSelected('#form input[name=payment]', 'Visa');
     * ?>
     * ```
     *
     * @param $selector
     * @param $optionText
     *
     * @return mixed
     * @see \Codeception\Lib\InnerBrowser::dontSeeOptionIsSelected()
     */
    public function dontSeeOptionIsSelected($select, $optionText) {
        return $this->scenario->runStep(new \Codeception\Step\Assertion('dontSeeOptionIsSelected', func_get_args()));
    }

 
    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Asserts that current page has 404 response status code.
     * Conditional Assertion: Test won't be stopped on fail
     * @see \Codeception\Lib\InnerBrowser::seePageNotFound()
     */
    public function canSeePageNotFound() {
        return $this->scenario->runStep(new \Codeception\Step\ConditionalAssertion('seePageNotFound', func_get_args()));
    }
    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Asserts that current page has 404 response status code.
     * @see \Codeception\Lib\InnerBrowser::seePageNotFound()
     */
    public function seePageNotFound() {
        return $this->scenario->runStep(new \Codeception\Step\Assertion('seePageNotFound', func_get_args()));
    }

 
    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Checks that response code is equal to value provided.
     *
     * @param $code
     *
     * @return mixed
     * Conditional Assertion: Test won't be stopped on fail
     * @see \Codeception\Lib\InnerBrowser::seeResponseCodeIs()
     */
    public function canSeeResponseCodeIs($code) {
        return $this->scenario->runStep(new \Codeception\Step\ConditionalAssertion('seeResponseCodeIs', func_get_args()));
    }
    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Checks that response code is equal to value provided.
     *
     * @param $code
     *
     * @return mixed
     * @see \Codeception\Lib\InnerBrowser::seeResponseCodeIs()
     */
    public function seeResponseCodeIs($code) {
        return $this->scenario->runStep(new \Codeception\Step\Assertion('seeResponseCodeIs', func_get_args()));
    }

 
    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Checks that the page title contains the given string.
     *
     * ``` php
     * <?php
     * $I->seeInTitle('Blog - Post #1');
     * ?>
     * ```
     *
     * @param $title
     *
     * @return mixed
     * Conditional Assertion: Test won't be stopped on fail
     * @see \Codeception\Lib\InnerBrowser::seeInTitle()
     */
    public function canSeeInTitle($title) {
        return $this->scenario->runStep(new \Codeception\Step\ConditionalAssertion('seeInTitle', func_get_args()));
    }
    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Checks that the page title contains the given string.
     *
     * ``` php
     * <?php
     * $I->seeInTitle('Blog - Post #1');
     * ?>
     * ```
     *
     * @param $title
     *
     * @return mixed
     * @see \Codeception\Lib\InnerBrowser::seeInTitle()
     */
    public function seeInTitle($title) {
        return $this->scenario->runStep(new \Codeception\Step\Assertion('seeInTitle', func_get_args()));
    }

 
    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Checks that the page title does not contain the given string.
     *
     * @param $title
     *
     * @return mixed
     * Conditional Assertion: Test won't be stopped on fail
     * @see \Codeception\Lib\InnerBrowser::dontSeeInTitle()
     */
    public function cantSeeInTitle($title) {
        return $this->scenario->runStep(new \Codeception\Step\ConditionalAssertion('dontSeeInTitle', func_get_args()));
    }
    /**
     * [!] Method is generated. Documentation taken from corresponding module.
     *
     * Checks that the page title does not contain the given string.
     *
     * @param $title
     *
     * @return mixed
     * @see \Codeception\Lib\InnerBrowser::dontSeeInTitle()
     */
    public function dontSeeInTitle($title) {
        return $this->scenario->runStep(new \Codeception\Step\Assertion('dontSeeInTitle', func_get_args()));
    }
}
