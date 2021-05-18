<?php

namespace Firebear\ConfigurableProducts\Plugin\Block\ConfigurableProduct\Product\View\Type;

class Configurable
{
    protected $catalogSession;

    protected $jsonEncoder;

    protected $jsonDecoder;
    private \Magento\Framework\Serialize\Serializer\Json $json;

    /**
     * @param \Magento\Catalog\Model\Session               $catalogSession
     * @param \Magento\Framework\Serialize\Serializer\Json $json
     */
    public function __construct(
        \Magento\Catalog\Model\Session $catalogSession,
        \Magento\Framework\Serialize\Serializer\Json $json
    ) {
        $this->catalogSession = $catalogSession;
        $this->json = $json;
    }

    /**
     * @param \Magento\ConfigurableProduct\Block\Product\View\Type\Configurable $subject
     * @param                                                                   $result
     *
     * @return string
     */
    public function afterGetJsonConfig(
        \Magento\ConfigurableProduct\Block\Product\View\Type\Configurable $subject,
        $result
    ) {

        $data = $this->catalogSession->getData('firebear_configurableproducts');

        if (isset($data['child_id'])) {

            $productId = $data['child_id'];
            $config = $this->json->unserialize($result);
            $defaultValues = [];

            foreach ($config['attributes'] as $attributeId => $attribute) {
                foreach ($attribute['options'] as $option) {
                    $optionId = $option['id'];
                    if (in_array($productId, $option['products'], false)) {
                        $defaultValues[$attributeId] = $optionId;
                    }
                }
            }

            $config['defaultValues'] = $defaultValues;
            $result = $this->json->serialize($config);
        }

        $this->catalogSession->getData('firebear_configurableproducts', true);

        return $result;
    }
}