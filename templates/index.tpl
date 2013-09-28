<section class="page-wrapper">
 {if isset($page_title) || isset($page_subtitle)}
 <header>
  {if isset($page_title)}<h1>{$page_title}</h1>{/if}
  {if isset($page_subtitle)}<h2>{$page_subtitle}</h2>{/if}

  <p class="vcard header-contact" role="contentinfo">
   <a class="org url organization-name" href="http://www.groningenbijles.nl/" title="GroningenBijles">GroningenBijles</a><br>
   <span class="fn">Taco de Wolff</span><br>
   <a class="tel" href="tel:+31621925183">+31 (0)6 219 251 83</a><br>
   <a class="email" href="mailto:info@groningenbijles.nl">info@groningenbijles.nl</a>
  </p>
 </header>
 {/if}

 {if isset($navigation)}
 <nav class="navigation" role="navigation">
  {$navigation}
 </nav>
 {/if}

 {if isset($main)}
 <article class="main" role="main">
  {$main}
 </article>
 {/if}

 <footer>
  <p class="vcard footer-contact" role="contentinfo">
   <a class="org url organization-name" href="http://www.groningenbijles.nl/" title="GroningenBijles">GroningenBijles</a><br>
   <span class="fn">Taco de Wolff</span><br>
   <a class="tel" href="tel:+31621925183">+31 (0)6 219 251 83</a><br>
   <a class="email" href="mailto:info@groningenbijles.nl">info@groningenbijles.nl</a>
  </p>
 </footer>
</section>