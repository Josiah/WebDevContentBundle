<?php namespace WebDev\ContentBundle\Controller;

// Dependencies
use CouponBundle\Form\PurchaseVoucherFormType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use WebDev\ContentBundle\Content\ContentContext;

// Annotations
use JMS\SecurityExtraBundle\Annotation\Secure;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class PageController
    extends Controller
{
    /**
     * @Route
     * @Template
     */
    public function viewAction( ContentContext $content )
    {
        return array('content');
    }
}