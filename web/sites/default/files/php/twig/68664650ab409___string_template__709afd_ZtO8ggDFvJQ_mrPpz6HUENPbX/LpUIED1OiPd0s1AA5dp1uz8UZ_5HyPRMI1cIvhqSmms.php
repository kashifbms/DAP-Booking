<?php

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Extension\CoreExtension;
use Twig\Extension\SandboxExtension;
use Twig\Markup;
use Twig\Sandbox\SecurityError;
use Twig\Sandbox\SecurityNotAllowedTagError;
use Twig\Sandbox\SecurityNotAllowedFilterError;
use Twig\Sandbox\SecurityNotAllowedFunctionError;
use Twig\Source;
use Twig\Template;
use Twig\TemplateWrapper;

/* __string_template__709afd9ce07cc7cd6b301de8ad3b2545 */
class __TwigTemplate_8af2043089ef04cdd8ecde1b73b95a78 extends Template
{
    private Source $source;
    /**
     * @var array<string, Template>
     */
    private array $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = [
        ];
        $this->sandbox = $this->extensions[SandboxExtension::class];
        $this->checkSecurity();
    }

    protected function doDisplay(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        // line 1
        yield "<div class=\"full-width mg-top-ex-md row no-pd-lr no-mg-lr views-row\">         
  <div class=\"col-md-4 ticket-img\">
";
        // line 3
        if (($context["field_image"] ?? null)) {
            // line 4
            yield "<img src=\"";
            yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(($context["field_image"] ?? null), 4, $this->source), "html", null, true);
            yield "\" alt=\"dynamic\">
";
        } else {
            // line 6
            yield "<img src =\"/themes/custom/dreamland/images/adult.png\" alt = \"staticImage\">
";
        }
        // line 8
        yield "</div>
  <div class=\"col-md-8\">                 
  <div class=\"product-container row\">
                                        <div class=\"product-title\">";
        // line 11
        yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(($context["title"] ?? null), 11, $this->source), "html", null, true);
        yield " </div>
                                        <div class=\"product-count\">";
        // line 12
        yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(($context["price__number"] ?? null), 12, $this->source), "html", null, true);
        yield " ";
        yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(($context["price__currency_code"] ?? null), 12, $this->source), "html", null, true);
        yield "</div>
                                    </div>
                                    <div class=\"full-width mg-top-ex-md view-row-body\">
                                        <div class=\"pro-description\">";
        // line 15
        yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(($context["body"] ?? null), 15, $this->source), "html", null, true);
        yield "</div>
                                    </div>    
";
        // line 17
        if ((($context["field_booking_tickets"] ?? null) != ($context["field_total_tickets"] ?? null))) {
            // line 18
            yield " 
 <div class=\"booking_date_bt row removepadding ticketcounter\" pid=\"";
            // line 19
            yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(($context["product_id"] ?? null), 19, $this->source), "html", null, true);
            yield "\">
<div class = \"btn-operators product-id\"></div>
                                            <div class=\"btn-operators ticketsub\" productId=\"";
            // line 21
            yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(($context["product_id"] ?? null), 21, $this->source), "html", null, true);
            yield "\">-</div>
                                            <div class=\"btn-operators-value ticketqty\" data-index=\"0\">0</div>
                                            <div class=\"btn-operators ticketadd\"  productId=\"";
            // line 23
            yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(($context["product_id"] ?? null), 23, $this->source), "html", null, true);
            yield "\">+</div>
                                        </div>
<div class=\"available-tickets\" data-tickets=\"";
            // line 25
            yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(($context["product_id"] ?? null), 25, $this->source), "html", null, true);
            yield "\">";
            yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(($context["field_available_tickets"] ?? null), 25, $this->source), "html", null, true);
            yield "</div>
<div class=\"more-tickets\" tickets-error=\"";
            // line 26
            yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(($context["product_id"] ?? null), 26, $this->source), "html", null, true);
            yield "\">No more tickets</div>
";
        }
        // line 28
        yield "</div>
</div>";
        $this->env->getExtension('\Drupal\Core\Template\TwigExtension')
            ->checkDeprecations($context, ["field_image", "title", "price__number", "price__currency_code", "body", "field_booking_tickets", "field_total_tickets", "product_id", "field_available_tickets"]);        yield from [];
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName(): string
    {
        return "__string_template__709afd9ce07cc7cd6b301de8ad3b2545";
    }

    /**
     * @codeCoverageIgnore
     */
    public function isTraitable(): bool
    {
        return false;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getDebugInfo(): array
    {
        return array (  113 => 28,  108 => 26,  102 => 25,  97 => 23,  92 => 21,  87 => 19,  84 => 18,  82 => 17,  77 => 15,  69 => 12,  65 => 11,  60 => 8,  56 => 6,  50 => 4,  48 => 3,  44 => 1,);
    }

    public function getSourceContext(): Source
    {
        return new Source("", "__string_template__709afd9ce07cc7cd6b301de8ad3b2545", "");
    }
    
    public function checkSecurity()
    {
        static $tags = array("if" => 3);
        static $filters = array("escape" => 4);
        static $functions = array();

        try {
            $this->sandbox->checkSecurity(
                ['if'],
                ['escape'],
                [],
                $this->source
            );
        } catch (SecurityError $e) {
            $e->setSourceContext($this->source);

            if ($e instanceof SecurityNotAllowedTagError && isset($tags[$e->getTagName()])) {
                $e->setTemplateLine($tags[$e->getTagName()]);
            } elseif ($e instanceof SecurityNotAllowedFilterError && isset($filters[$e->getFilterName()])) {
                $e->setTemplateLine($filters[$e->getFilterName()]);
            } elseif ($e instanceof SecurityNotAllowedFunctionError && isset($functions[$e->getFunctionName()])) {
                $e->setTemplateLine($functions[$e->getFunctionName()]);
            }

            throw $e;
        }

    }
}
