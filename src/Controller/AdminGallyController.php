<?php

declare(strict_types=1);

namespace Gally\SyliusPlugin\Controller;

use Gally\Rest\ApiException;
use Gally\SyliusPlugin\Api\AuthenticationTokenProvider;
use Gally\SyliusPlugin\Form\Type\GallyConfigurationType;
use Gally\SyliusPlugin\Form\Type\SyncSourceFieldsType;
use Gally\SyliusPlugin\Form\Type\TestConnectionType;
use Gally\SyliusPlugin\Repository\GallyConfigurationRepository;
use Gally\SyliusPlugin\Synchronizer\MetadataSynchronizer;
use Gally\SyliusPlugin\Synchronizer\SourceFieldSynchronizer;
use ReflectionClass;
use Sylius\Bundle\ResourceBundle\Controller\ControllerTrait;
use Sylius\Component\Product\Model\ProductAttribute;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

final class AdminGallyController extends AbstractController
{
    public function __construct(
        private GallyConfigurationRepository $gallyConfigurationRepository,
        private AuthenticationTokenProvider $authenticationTokenProvider,
        private RepositoryInterface $productAttributeRepository,
        private MetadataSynchronizer $metadataSynchronizer,
        private SourceFieldSynchronizer $sourceFieldSynchronizer,
        private TranslatorInterface $translator,
    ) {
    }

    public function renderGallyConfigForm(Request $request): Response
    {
        $gallyConfiguration = $this->gallyConfigurationRepository->getConfiguration();
        $configForm = $this->createForm(GallyConfigurationType::class, $gallyConfiguration);
        $configForm->handleRequest($request);

        if($configForm->isSubmitted() && $configForm->isValid()) {
            $gallyConfiguration = $configForm->getData();

            $this->gallyConfigurationRepository->add($gallyConfiguration);
            $this->addFlash('success', $this->translator->trans('gally_sylius_plugin.ui.configuration_saved'));
        }

        return $this->render('@GallySyliusPlugin/Config/_form.html.twig', [
            'connectionForm' => $configForm->createView(),
            'testForm' => $this->createForm(TestConnectionType::class),
            'syncForm' => $this->createForm(SyncSourceFieldsType::class),
        ]);
    }

    public function renderTestConnectionForm(Request $request): Response
    {
        $testForm = $this->createForm(TestConnectionType::class);
        $testForm->handleRequest($request);

        if($testForm->isSubmitted() && $testForm->isValid()) {
            try {
                $configuration = $this->gallyConfigurationRepository->getConfiguration();
                $this->authenticationTokenProvider->getAuthenticationToken(
                    $configuration->getBaseUrl(),
                    $configuration->getUserName(),
                    $configuration->getPassword(),
                );
                $this->addFlash('success', $this->translator->trans('gally_sylius_plugin.ui.test_connection_success'));
            } catch (ApiException $e) {
                $this->addFlash('error', $this->translator->trans('gally_sylius_plugin.ui.test_connection_failure'));
            }
        }

        return $this->render('@GallySyliusPlugin/Config/_form.html.twig', [
            'connectionForm' => $this->createForm(GallyConfigurationType::class),
            'testForm' => $testForm,
            'syncForm' => $this->createForm(SyncSourceFieldsType::class)
        ]);
    }

    public function renderSyncFieldsForm(Request $request): Response
    {
        $syncForm = $this->createForm(SyncSourceFieldsType::class);
        $syncForm->handleRequest($request);

        if($syncForm->isSubmitted() && $syncForm->isValid()) {
            $attributes = $this->productAttributeRepository->findAll();
            $metadataName = (new ReflectionClass(ProductAttribute::class))->getShortName();
            $metadata = $this->metadataSynchronizer->synchronizeItem(['entity' => $metadataName]);
            foreach ($attributes as $attribute) {
                $this->sourceFieldSynchronizer->synchronizeItem([
                    'metadata' => $metadata,
                    'field' => [
                        'code' => $attribute->getCode(),
                        'type' => SourceFieldSynchronizer::getGallyType($attribute->getType()),
                    ]
                ]);
            }

            $this->addFlash('success', $this->translator->trans('gally_sylius_plugin.ui.sync_success'));
        }

        return $this->render('@GallySyliusPlugin/Config/_form.html.twig', [
            'connectionForm' =>  $this->createForm(GallyConfigurationType::class),
            'testForm' => $this->createForm(TestConnectionType::class),
            'syncForm' => $syncForm,
        ]);
    }

    protected function get(string $id)
    {
        return $this->container->get($id);
    }
}
