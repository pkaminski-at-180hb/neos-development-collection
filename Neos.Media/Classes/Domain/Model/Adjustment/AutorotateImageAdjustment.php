<?php
declare(strict_types=1);

namespace Neos\Media\Domain\Model\Adjustment;

/*
 * This file is part of the Neos.Media package.
 *
 * (c) Contributors of the Neos Project - www.neos.io
 *
 * This package is Open Source Software. For the full copyright and license
 * information, please view the LICENSE file which was distributed with this
 * source code.
 */

use Neos\Flow\Annotations as Flow;
use Imagine\Image\ImageInterface as ImagineImageInterface;
use Imagine\Image\ManipulatorInterface;
use Imagine\Imagick\Image as ImagickImage;
use Imagine\Filter\Basic\Autorotate;

use Neos\Utility\ObjectAccess;


/**
 * An adjustment for autorotating an image if it has orientation tag with value different than 1
 *
 * @Flow\Entity
 */
class AutorotateImageAdjustment extends AbstractImageAdjustment
{
    /**
     * @var integer
     */
    protected $position = 1;

    /**
     * Check if this Adjustment can or should be applied to its ImageVariant.
     *
     * @param ImagineImageInterface $image
     * @return boolean
     */
    public function canBeApplied(ImagineImageInterface $image)
    {
        $imageMetadata = $image->metadata();
        return isset($imageMetadata['ifd0.Orientation']) && $imageMetadata['ifd0.Orientation'] !== 1;
    }

    /**
     * Applies this adjustment to the given Imagine Image object
     *
     * @param ImagineImageInterface $image
     * @return ImagineImageInterface|ManipulatorInterface
     * @internal Should never be used outside of the media package. Rely on the ImageService to apply your adjustments.
     */
    public function applyToImage(ImagineImageInterface $image)
    {
        return $this->autorotate($image);
    }

    /**
     * Applies imagine autorotate filter
     *
     * @param ImagineImageInterface $image
     * @return ImagineImageInterface
     * @throws \ImagickException
     */
    protected function autorotate(ImagineImageInterface $image)
    {
        $imageMetadata = $image->metadata();

        if (isset($imageMetadata['ifd0.Orientation']) && $imageMetadata['ifd0.Orientation'] !== 1) {
            $autorotateFilter = new Autorotate();
            $image = $autorotateFilter->apply($image);
            $imageMetadata['ifd0.Orientation'] = 1;

            // @todo refactor once Imagine library gets api for setting orientation tag
            if ($image instanceof ImagickImage) {
                /**
                 * @var \Imagick $imagick
                 */
                $imagick = ObjectAccess::getProperty($image, 'imagick');
                $imagick->setImageOrientation(1);
            }
        }

        return $image;
    }

}
