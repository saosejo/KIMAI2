<?php

/*
 * This file is part of the Kimai time-tracking app.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Invoice\Renderer;

use App\Entity\InvoiceDocument;
use App\Invoice\InvoiceFilename;
use App\Invoice\InvoiceModel;
use App\Utils\HtmlToPdfConverter;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Twig\Environment;

final class PdfRenderer extends AbstractTwigRenderer
{
    /**
     * @var HtmlToPdfConverter
     */
    private $converter;

    public function __construct(Environment $twig, HtmlToPdfConverter $converter)
    {
        parent::__construct($twig);
        $this->converter = $converter;
    }

    public function supports(InvoiceDocument $document): bool
    {
        return stripos($document->getFilename(), '.pdf.twig') !== false;
    }

    public function render(InvoiceDocument $document, InvoiceModel $model): Response
    {
        $content = $this->renderTwigTemplate($document, $model);

        $content = $this->converter->convertToPdf($content, [
            'setAutoTopMargin' => 'pad',
            'setAutoBottomMargin' => 'pad',
            'margin_top' => 12,
            'margin_bottom' => 8,
        ]);

        $filename = (string) new InvoiceFilename($model);

        $response = new Response($content);

        $disposition = $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $filename . '.pdf');

        $response->headers->set('Content-Type', 'application/pdf');
        $response->headers->set('Content-Disposition', $disposition);

        return $response;
    }
}
