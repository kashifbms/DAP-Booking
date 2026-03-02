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

/* modules/contrib/commerce/modules/order/templates/commerce-order--user.html.twig */
class __TwigTemplate_6a6852470972daf730c104160188629d extends Template
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
        // line 20
        yield "<div";
        yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(($context["attributes"] ?? null), 20, $this->source), "html", null, true);
        yield ">
  <div class=\"customer-information\">
    ";
        // line 22
        yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(CoreExtension::getAttribute($this->env, $this->source, ($context["order"] ?? null), "mail", [], "any", false, false, true, 22), 22, $this->source), "html", null, true);
        yield "
    ";
        // line 23
        if (CoreExtension::getAttribute($this->env, $this->source, ($context["order"] ?? null), "shipping_information", [], "any", false, false, true, 23)) {
            // line 24
            yield "      <div class=\"customer-information__shipping\">
        <div class=\"field__label\">";
            // line 25
            yield $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(t("Shipping information"));
            yield "</div>
        ";
            // line 26
            yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(CoreExtension::getAttribute($this->env, $this->source, ($context["order"] ?? null), "shipping_information", [], "any", false, false, true, 26), 26, $this->source), "html", null, true);
            yield "
      </div>
    ";
        }
        // line 29
        yield "    ";
        if (CoreExtension::getAttribute($this->env, $this->source, ($context["order"] ?? null), "billing_information", [], "any", false, false, true, 29)) {
            // line 30
            yield "      <div class=\"customer-billing\">
        <div class=\"field__label\">";
            // line 31
            yield $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(t("Billing information"));
            yield "</div>
        ";
            // line 32
            yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(CoreExtension::getAttribute($this->env, $this->source, ($context["order"] ?? null), "billing_information", [], "any", false, false, true, 32), 32, $this->source), "html", null, true);
            yield "
      </div>
    ";
        }
        // line 35
        yield "  </div>
  <div class=\"order-information\">
    ";
        // line 37
        yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(CoreExtension::getAttribute($this->env, $this->source, ($context["order"] ?? null), "completed", [], "any", false, false, true, 37), 37, $this->source), "html", null, true);
        yield "
    ";
        // line 38
        yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(CoreExtension::getAttribute($this->env, $this->source, ($context["order"] ?? null), "placed", [], "any", false, false, true, 38), 38, $this->source), "html", null, true);
        yield "
    ";
        // line 39
        yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(CoreExtension::getAttribute($this->env, $this->source, ($context["order"] ?? null), "state", [], "any", false, false, true, 39), 39, $this->source), "html", null, true);
        yield "
    ";
        // line 40
        yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(CoreExtension::getAttribute($this->env, $this->source, ($context["order"] ?? null), "order_items", [], "any", false, false, true, 40), 40, $this->source), "html", null, true);
        yield "
    ";
        // line 41
        yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(CoreExtension::getAttribute($this->env, $this->source, ($context["order"] ?? null), "total_price", [], "any", false, false, true, 41), 41, $this->source), "html", null, true);
        yield "
    ";
        // line 42
        yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(($context["additional_order_fields"] ?? null), 42, $this->source), "html", null, true);
        yield "
  </div>
</div>
";
        $this->env->getExtension('\Drupal\Core\Template\TwigExtension')
            ->checkDeprecations($context, ["attributes", "order", "additional_order_fields"]);        yield from [];
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName(): string
    {
        return "modules/contrib/commerce/modules/order/templates/commerce-order--user.html.twig";
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
        return array (  109 => 42,  105 => 41,  101 => 40,  97 => 39,  93 => 38,  89 => 37,  85 => 35,  79 => 32,  75 => 31,  72 => 30,  69 => 29,  63 => 26,  59 => 25,  56 => 24,  54 => 23,  50 => 22,  44 => 20,);
    }

    public function getSourceContext(): Source
    {
        return new Source("", "modules/contrib/commerce/modules/order/templates/commerce-order--user.html.twig", "/var/www/vhosts/booking.dreamlanduae.com/httpdocs/web/modules/contrib/commerce/modules/order/templates/commerce-order--user.html.twig");
    }
    
    public function checkSecurity()
    {
        static $tags = array("if" => 23);
        static $filters = array("escape" => 20, "t" => 25);
        static $functions = array();

        try {
            $this->sandbox->checkSecurity(
                ['if'],
                ['escape', 't'],
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
