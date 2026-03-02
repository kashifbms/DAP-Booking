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

/* themes/custom/dreamland/templates/layout/page.html.twig */
class __TwigTemplate_8bea9e622bfeba8250afcb85bfce9c23 extends Template
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
        // line 27
        yield "
<div id=\"page-wrapper\" class=\"page-wrapper\">
  <div id=\"page\">
    ";
        // line 31
        yield "    <header
      id=\"header\"
      class=\"site-header\"
      data-drupal-selector=\"site-header\"
      role=\"banner\"
    >
      ";
        // line 37
        yield " ";
        // line 95
        yield "
      <div class=\"row\">
        ";
        // line 97
        if (($context["logo"] ?? null)) {
            // line 98
            yield "        <div class=\"col-lg-2 logo-img\">
          ";
            // line 100
            yield "          <a href=\"/\">
            <img src=\"";
            // line 101
            yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(($context["logo"] ?? null), 101, $this->source), "html", null, true);
            yield "\" alt=\"logo\" />
          </a>
        </div>
        ";
        }
        // line 105
        yield "        <div class=\"banner-container no-pd-lr\" style=\" background:  url('";
        ((($context["bannerURL"] ?? null)) ? (yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, ($context["bannerURL"] ?? null), "html", null, true)) : (yield "/themes/custom/dreamland/images/banner.png"));
        yield "');  width: 100%;
  height: 425px;
  background-repeat: no-repeat;
  background-size: cover;
  max-width: 100%;\"></div>
      </div>
    </header>
    ";
        // line 113
        yield "    <div class=\"heighlighted\">
      ";
        // line 114
        yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(CoreExtension::getAttribute($this->env, $this->source, ($context["page"] ?? null), "highlighted", [], "any", false, false, true, 114), 114, $this->source), "html", null, true);
        yield "
    </div>
    ";
        // line 116
        if (CoreExtension::getAttribute($this->env, $this->source, ($context["page"] ?? null), "content_above", [], "any", false, false, true, 116)) {
            // line 117
            yield "    <div class=\"content-above\">
              <div class=\"main-content__container container\">
      ";
            // line 119
            yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(CoreExtension::getAttribute($this->env, $this->source, ($context["page"] ?? null), "content_above", [], "any", false, false, true, 119), 119, $this->source), "html", null, true);
            yield "
      </div>
    </div>
    ";
        }
        // line 123
        yield "    <div id=\"main-wrapper\" class=\"layout-main-wrapper layout-container\">
      <div id=\"main\" class=\"layout-main\">
        <div class=\"main-content main-content-layout\">
          <a id=\"main-content\" tabindex=\"-1\"></a>
          <div class=\"main-content__container_dreamland container\">
            ";
        // line 128
        if (CoreExtension::getAttribute($this->env, $this->source, ($context["page"] ?? null), "sidebar", [], "any", false, false, true, 128)) {
            // line 129
            yield "            <div class=\"sidebar-wrapper\">
              <main role=\"main\" class=\"site-main\">
                ";
            // line 131
            yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(CoreExtension::getAttribute($this->env, $this->source, ($context["page"] ?? null), "content", [], "any", false, false, true, 131), 131, $this->source), "html", null, true);
            yield "
              </main>
              ";
            // line 133
            yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(CoreExtension::getAttribute($this->env, $this->source, ($context["page"] ?? null), "sidebar", [], "any", false, false, true, 133), 133, $this->source), "html", null, true);
            yield "
            </div>
            ";
        } else {
            // line 136
            yield "            <main role=\"main\">
              ";
            // line 137
            yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(CoreExtension::getAttribute($this->env, $this->source, ($context["page"] ?? null), "content", [], "any", false, false, true, 137), 137, $this->source), "html", null, true);
            yield "
            </main>
            ";
        }
        // line 140
        yield "            ";
        yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(CoreExtension::getAttribute($this->env, $this->source, ($context["page"] ?? null), "content_below", [], "any", false, false, true, 140), 140, $this->source), "html", null, true);
        yield "
          </div>
        </div>
      </div>
    </div>

    ";
        // line 146
        if (CoreExtension::getAttribute($this->env, $this->source, ($context["page"] ?? null), "footer_bottom", [], "any", false, false, true, 146)) {
            // line 147
            yield "    <footer class=\"footer-sidebar\">
      ";
            // line 151
            yield "        <div class=\"d-flex justify-content-between py-4 footer-bottom-bg pd-lr\">
          ";
            // line 152
            yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(CoreExtension::getAttribute($this->env, $this->source, ($context["page"] ?? null), "footer_bottom", [], "any", false, false, true, 152), 152, $this->source), "html", null, true);
            yield "
          ";
            // line 156
            yield "      </div>
    </footer>
    ";
        }
        // line 159
        yield "
    <div class=\"overlay\" data-drupal-selector=\"overlay\"></div>
  </div>
</div>
";
        $this->env->getExtension('\Drupal\Core\Template\TwigExtension')
            ->checkDeprecations($context, ["logo", "bannerURL", "page"]);        yield from [];
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName(): string
    {
        return "themes/custom/dreamland/templates/layout/page.html.twig";
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
        return array (  170 => 159,  165 => 156,  161 => 152,  158 => 151,  155 => 147,  153 => 146,  143 => 140,  137 => 137,  134 => 136,  128 => 133,  123 => 131,  119 => 129,  117 => 128,  110 => 123,  103 => 119,  99 => 117,  97 => 116,  92 => 114,  89 => 113,  78 => 105,  71 => 101,  68 => 100,  65 => 98,  63 => 97,  59 => 95,  57 => 37,  49 => 31,  44 => 27,);
    }

    public function getSourceContext(): Source
    {
        return new Source("", "themes/custom/dreamland/templates/layout/page.html.twig", "/var/www/vhosts/booking.dreamlanduae.com/httpdocs/web/themes/custom/dreamland/templates/layout/page.html.twig");
    }
    
    public function checkSecurity()
    {
        static $tags = array("if" => 97);
        static $filters = array("escape" => 101);
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
