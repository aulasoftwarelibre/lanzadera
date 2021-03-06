<?php

namespace AppBundle\Admin;

use Doctrine\ORM\QueryBuilder;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class ProductAdmin extends Admin
{
	/**
	 * {@inheritdoc}
	 */
	protected $baseRouteName = "lanzadera_product";

	/**
	 * {@inheritdoc}
	 */
	protected $baseRoutePattern = 'lanzadera/product';
    /**
     * {@inheritdoc}
     */
    protected $datagridValues = array(
        '_page' => 1,            // display the first page (default = 1)
        '_sort_order' => 'ASC', // reverse order (default = 'ASC')
        '_sort_by' => 'name'  // name of the ordered field
    );

    /**
     * {@inheritdoc}
     */
    public function createQuery($context = 'list')
    {
        /** @var QueryBuilder $query */
        $query = parent::createQuery($context);
        $alias = current($query->getRootAliases());
        $query->leftJoin($alias . '.organization', 'o2');
        $query->leftJoin($alias . '.tags', 't');
        $query->leftJoin($alias . '.category', 'c');
        $query->leftJoin($alias . '.certificates', 'q');
        $query->leftJoin('q.classification', 'r');
        $query->addSelect('partial o2.{id, name, enabled}');
        $query->addSelect('t');
        $query->addSelect('c');
        $query->addSelect('q');
        $query->addSelect('partial r.{id, name}');
        return $query;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('name', null, array(
                    'label' => 'label.name'
            ))
            ->add('description', null, array(
                    'label' => 'label.description'
            ))
            ->add('status', null, array(
                    'label' => 'label.status'
                ), 'status', array(
                    'constraints' => array()
                )
            )
            ->add('certificates.classification', null, array(
                    'label' => 'label.certificate'
                ), null,
                array(
                    'expanded' => false,
                    'multiple' => false,
            ))
            ->add('category', null, array(
                    'label' => 'label.category'
                ), null,
                array(
                    'expanded' => false,
                    'multiple' => true,
                    'query_builder' => $this->getRepository('taxon')->createTaxonQuery('Category'),
            ))
            ->add('tags', null, array(
                    'label' => 'label.tags'
                ), null,
                array(
                    'expanded' => false,
                    'multiple' => true,
                    'query_builder' => $this->getRepository('taxon')->createTaxonQuery('Tag'),
            ))
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('name', null, array(
                    'label' => 'label.name'
            ))
            ->add('organization.name', null, array(
                    'label' => 'label.organization'
            ))
            ->add('certificates', null, array(
                    'label' => 'label.certificate'
            ))
            ->add('_action', 'actions', array(
                'actions' => array(
                    'show' => array(),
                    'edit' => array(),
                    'delete' => array(),
                )
            ))
        ;
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('group.product_description', array('class' => 'col-md-6'))
                ->add('name', null, array(
                        'label' => 'label.name',
                        'required' => true,
                ))
                ->add('description', 'textarea', array(
                        'label' => 'label.description'
                ))
                ->add('status', 'status', array(
                        'label' => 'label.status',
                        'help' => 'help.status',
                        'disabled' => false === $this->isGranted('STATUS')
                ))
                ->add('organization', null, array(
                        'label' => 'label.organization',
                        'help' => 'help.organization',
                        'required' => true,
                        'attr' => array(
                            'placeholder' => 'product.organization_placeholder',
                            'class' => 'form-control',
                        )
                ))
                ->add('regularPrice', 'money', array(
                        'label' => 'label.regular_price',
                        'required' => false,
                ))
                ->add('reducedPrice', 'money', array(
                        'label' => 'label.reduced_price',
                        'required' => false,
                ))
            ->end()
	        ->with('group.image', array('class' => 'col-md-6'))
		        ->add('media', 'sonata_media_type', array(
			        'label' => false,
			        'required' => false,
			        'provider' => 'sonata.media.provider.image',
			        'data_class'   =>  'Application\Sonata\MediaBundle\Entity\Media',
			        'context'  => 'default'
		        ))
	        ->end()
            ->with('group.metadata', array('class' => 'col-md-6'))
                 ->add('category', 'sonata_type_model', array(
                        'label' => 'label.category',
                        'help' => 'help.category',
                        'query' => $this->getRepository('taxon')->createTaxonQuery('Category'),
                        'btn_add' => false,
                        'required' => true,
                        'attr' => array(
                            'placeholder' => 'label.category_placeholder',
                            'class' => 'form-control'
                        )
                    ),
                    array(
                        'admin_code' => 'lanzadera.admin.category',
                    )
                )
                ->add('tags', 'sonata_type_model', array(
                        'label' => 'label.tags',
                        'help' => 'help.tag',
                        'query' => $this->getRepository('taxon')->createTaxonQuery('Tag'),
                        'expanded' => false,
                        'multiple' => true,
                        'btn_add' => $this->trans('label.tag_add'),
                        'required' => false,
                        'attr' => array(
                            'placeholder' => 'label.tag_placeholder',
                            'class' => 'form-control'
                        )
                    ),
                    array(
                        'admin_code' => 'lanzadera.admin.tag'
                    )
                )
                ->add('autoCertificates', 'text', array(
                        'label' => 'label.certificates_auto',
                        'help' => 'help.certificates_auto',
                        'required' => false,
                        'disabled' => true,
                ))
                ->add('certificates', 'certificate', array(
                        'label' => 'label.certificates_manual',
                        'help' => 'help.certificates_manual',
                        'required' => false,
                        'attr' => array(
                            'placeholder' => 'label.certificate_placeholder',
                            'class' => 'form-control'
                      )
                ))
            ->end()
            ->with('group.indicators', array('class' => 'col-md-6'))
                ->add('indicators', 'product_indicator', array(
                    'label' => 'label.indicators',
                    'block_name' => 'lanzadera_indicator'
                ))
            ->end()
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->with('group.product_description', array('class' => 'col-md-6'))
                ->add('name', null, array(
                        'label' => 'label.name'
                ))
	            ->add('slug', null, array(
		                'label' => 'label.slug'
	            ))
                ->add('description', null, array(
                        'label' => 'label.description'
                ))
                ->add('status', 'string', array(
                    'label' => 'label.status',
                    'template' => 'Product/CRUD/show_status.html.twig'
                ))
                ->add('organization.name', null, array(
                    'label' => 'label.organization'
                ))
                ->add('category.name', null, array(
                        'label' => 'label.category'
                ))
                ->add('tags_as_list', null, array(
                        'label' => 'label.tags'
                ))
                ->add('certificates', 'collection', array(
                        'label' => 'label.certificate',
                ))
            ->end()
            ->with('group.image', array('class' => 'col-md-6'))
                ->add('media', null, array(
                    'label' => ' ',
                    'template' => 'Product/CRUD/show_media.html.twig',
                ))
            ->end()
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function postPersist($object)
    {
        $this->getConfigurationPool()->getContainer()->get('sonata.notification.backend')->createAndPublish('backend', array(
                'classification' => 'all',
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function postUpdate($object)
    {
        $this->getConfigurationPool()->getContainer()->get('sonata.notification.backend')->createAndPublish('backend', array(
                'classification' => 'all',
            )
        );
    }
}
