<?php
/**
 * Created by PhpStorm.
 * User: sergio
 * Date: 14/06/14
 * Time: 22:57
 */

namespace Tejedora\LanzaderaBundle\Behat;


class WebContext extends DefaultContext
{
    /**
     * @When estoy en el panel de administración
     */
    public function estoyEnElPanelDeAdministracion()
    {
        $this->getSession()->visit($this->generateUrl('sonata_admin_dashboard'));
    }

    /**
     * @Then debo estar en el panel de administración
     */
    public function deboEstarEnElPanelDeAdministracion()
    {
        $this->assertSession()->addressEquals($this->generateUrl('sonata_admin_dashboard'));
    }

    /**
     * @When presiono :link en el bloque :block
     */
    public function presionoEnElBloque($link, $block)
    {
        $node = $this->getSession()->getPage()->find('xpath', "//tr/td[contains(text(),'{$block}')]/..");
        $node->clickLink($link);
    }
} 