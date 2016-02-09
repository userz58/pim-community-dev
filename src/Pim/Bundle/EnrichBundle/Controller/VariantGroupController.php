<?php

namespace Pim\Bundle\EnrichBundle\Controller;

use Akeneo\Component\StorageUtils\Remover\RemoverInterface;
use Oro\Bundle\SecurityBundle\Annotation\AclAncestor;
use Pim\Bundle\CatalogBundle\Entity\Group;
use Pim\Bundle\CatalogBundle\Factory\GroupFactory;
use Pim\Bundle\CatalogBundle\Manager\GroupManager;
use Pim\Bundle\CatalogBundle\Manager\VariantGroupAttributesResolver;
use Pim\Bundle\EnrichBundle\Form\Handler\HandlerInterface;
use Pim\Component\Catalog\Model\AvailableAttributes;
use Pim\Component\Catalog\Model\GroupInterface;
use Pim\Component\Catalog\Repository\AttributeRepositoryInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Variant group controller
 *
 * @author    Romain Monceau <romain@akeneo.com>
 * @copyright 2013 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class VariantGroupController extends GroupController
{
    /** @var AttributeRepositoryInterface */
    protected $attributeRepository;

    /** @var VariantGroupAttributesResolver */
    protected $groupAttrResolver;

    /**
     * @param GroupManager                   $groupManager
     * @param HandlerInterface               $groupHandler
     * @param Form                           $groupForm
     * @param GroupFactory                   $groupFactory
     * @param AttributeRepositoryInterface   $attributeRepository
     * @param VariantGroupAttributesResolver $groupAttrResolver
     * @param RemoverInterface               $groupRemover
     */
    public function __construct(
        GroupManager $groupManager,
        HandlerInterface $groupHandler,
        Form $groupForm,
        GroupFactory $groupFactory,
        AttributeRepositoryInterface $attributeRepository,
        VariantGroupAttributesResolver $groupAttrResolver,
        RemoverInterface $groupRemover
    ) {
        parent::__construct(
            $groupManager,
            $groupHandler,
            $groupForm,
            $groupFactory,
            $groupRemover
        );

        $this->attributeRepository = $attributeRepository;
        $this->groupAttrResolver   = $groupAttrResolver;
    }

    /**
     * {@inheritdoc}
     *
     * @Template
     * @AclAncestor("pim_enrich_variant_group_index")
     *
     * @return Response
     */
    public function indexAction(Request $request)
    {
        return [
            'groupTypes' => array_keys($this->groupManager->getTypeChoices(true))
        ];
    }

    /**
     * {@inheritdoc}
     *
     * @Template
     * @AclAncestor("pim_enrich_variant_group_create")
     */
    public function createAction(Request $request)
    {
        if (!$request->isXmlHttpRequest()) {
            return $this->redirectToRoute('pim_enrich_variant_group_index');
        }

        $group = $this->groupFactory->createGroup('VARIANT');

        if ($this->groupHandler->process($group)) {
            $this->addFlash('success', 'flash.variant group.created');

            $url = $this->generateUrl(
                'pim_enrich_variant_group_edit',
                ['id' => $group->getId()]
            );
            $response = ['status' => 1, 'url' => $url];

            return new Response(json_encode($response));
        }

        return [
            'form' => $this->groupForm->createView()
        ];
    }

    /**
     * {@inheritdoc}
     *
     * TODO: find a way to use param converter with interfaces
     *
     * @AclAncestor("pim_enrich_variant_group_edit")
     * @Template
     */
    public function editAction(Group $group)
    {
        if (!$group->getType()->isVariant()) {
            throw new NotFoundHttpException(sprintf('Variant group with id %d not found.', $group->getId()));
        }

        if ($this->groupHandler->process($group)) {
            $this->addFlash('success', 'flash.variant group.updated');
        }

        return [
            'form'           => $this->groupForm->createView(),
            'currentGroup'   => $group->getId(),
            'attributesForm' => $this->getAvailableAttributesForm($group)->createView(),
        ];
    }

    /**
     * Get the AvailbleAttributes form
     *
     * @param GroupInterface $group
     *
     * @return \Symfony\Component\Form\Form
     */
    protected function getAvailableAttributesForm(GroupInterface $group)
    {
        return $this->createForm(
            'pim_available_attributes',
            new AvailableAttributes(),
            ['excluded_attributes' => $this->groupAttrResolver->getNonEligibleAttributes($group)]
        );
    }
}
