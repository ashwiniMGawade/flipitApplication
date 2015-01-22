$(document).ready(function() {
   $("#disqus_thread").show();
   $("#disqusComments").remove();
});
(function() {
    var dsq = document.createElement('script'); dsq.type = 'text/javascript'; dsq.async = true;
    var count = document.createElement('script'); dsq.type = 'text/javascript'; dsq.async = true;
    dsq.src = '//' + disqus_shortname + '.disqus.com/embed.js';
    count.src = '//' + disqus_shortname + '.disqus.com/count.js';
    (document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(dsq);
    (document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(count);
})();