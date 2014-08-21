<?php
/**
 * Created by PhpStorm.
 * User: sergio
 * Date: 14/06/14
 * Time: 22:57
 */

namespace Lanzadera\CoreBundle\Behat;


use Behat\Gherkin\Node\TableNode;

class WebContext extends DefaultContext
{
    /**
     * @Given que estoy en la página del escritorio
     */
    public function iAmOnDashboard()
    {
        $this->getSession()->visit($this->generateUrl('sonata_admin_dashboard'));
    }

    /**
     * @Then debo estar en la página del escritorio
     * @Then debería estar en la página del escritorio
     */
    public function iShouldBeOnDashBoard()
    {
        $this->assertSession()->addressEquals($this->generateUrl('sonata_admin_dashboard'));
    }

    /**
     * @Given /^que estoy en la página (principal|creación) de (.*)$/
     */
    public function iAmOnActionResource($action, $resource)
    {
        $page = sprintf("lanzadera_%s_%s", $this->translate[$resource], $this->actions[$action]);
        $this->iAmOnPage($page);
    }

    /**
     * @Given /^que estoy en la página (mostrar|edición) de ([^"]*) con ([^"]*) "([^""]*)"$/
     */
    public function iAmDoingSomethingWithResource($action, $type, $property, $value)
    {
        $type = $this->translate[$type];
        $property = $this->translate[$property];

        $action = str_replace(array_keys($this->actions), array_values($this->actions), $action);
        $resource = $this->findOneBy($type, array($property => $value));

        $this->getSession()->visit($this->generateUrl(sprintf('lanzadera_%s_%s', $type, $action), array('id' => $resource->getId())));
    }


    /**
     * @Then /^debería estar en la página (mostrar|edición) de ([^"]*) con ([^"]*) "([^""]*)"$/
     */
    public function iShouldBeDoingSomethingWithResource($action, $type, $property, $value)
    {
        $type = $this->translate[$type];
        $property = $this->translate[$property];

        $action = str_replace(array_keys($this->actions), array_values($this->actions), $action);
        $resource = $this->findOneBy($type, array($property => $value));

        $this->assertSession()->addressEquals($this->generateUrl(sprintf('lanzadera_%s_%s', $type, $action), array('id' => $resource->getId())));
        $this->assertSession()->statusCodeEquals(200);
    }

    /**
     * @Then /^debería estar en la página (principal|creación) de (.*)$/
     * @Then /^debería estar todavía en la página (principal|creación) de (.*)$/
     */
    public function iShouldBeOnActionResource($action, $resource)
    {
        $page = sprintf("lanzadera_%s_%s", $this->translate[$resource], $this->actions[$action]);
        $this->iShouldBeOnPage($page);
    }


    /**
     * @Given estoy en la página :page
     */
    public function iAmOnPage($page)
    {
        $this->getSession()->visit($this->generateUrl($page));
    }

    /**
     * @Then debería estar en la página del usuario con nombre :username
     */
    public function iShouldBeOnTheUserPage($username)
    {
        $this->iShouldBeOnTheResourcePage('user', 'username', $username);
    }

    /**
     * @Then debería estar en la página :page
     */
    public function iShouldBeOnPage($page)
    {
        $this->assertSession()->addressEquals($this->generateUrl($page));
        $this->assertSession()->statusCodeEquals(200);
    }

    /**
     * @When presiono :action junto a :block
     */
    public function iClickNear($action, $block)
    {
        $node = $this->getSession()->getPage()->find('xpath', "//tr/td[contains(text(),'{$block}')]/..");
        $node->clickLink($action);
    }

    /**
     * @When presiono :action los :block
     * @When presiono :action las :block
     */
    public function iClickActionOnBlock($action, $block)
    {
        $action = ucfirst($action);
        $block = ucfirst($block);
        $this->iClickNear($action, $block);

    }

    /**
     * @Then debería estar en la sección :block
     */
    public function deboEstarEnlaSeccion($block)
    {
        $this->assertSession()->elementExists('xpath', sprintf("//ol[contains(@class, 'breadcrumb')]//li[contains(@class, 'active')]//span[contains(text(),'%s')]", $block));
    }

    /**
     * @Then /^debería ver (\d+) (usuarios|usuario) en la lista$/
     */
    public function iShouldSeeNumItems($num)
    {
        $this->assertSession()->pageTextContains(sprintf("%d %s", $num, $num == 1 ? "resultado" : "resultados"));
    }

    /**
     * @When relleno el formulario con
     */
    public function iFillTheForm(TableNode $tableNode)
    {

    }

    /**
     * @param $type
     * @param $property
     * @param $value
     */
    protected  function iShouldBeOnTheResourcePage($type, $property, $value)
    {
        $resource = $this->findOneBy($type, array($property => $value));

        $this->assertSession()->addressEquals($this->generateUrl(sprintf('lanzadera_%s_show', $type), array('id' => $resource->getId())));
        $this->assertSession()->statusCodeEquals(200);
    }



} 