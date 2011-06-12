
//función inicializadora del script
$(document).ready(function(){
        $(".jerarquia_reorganiza li span.apartado").draggable({
                revert: true,
				containment:'window',
				scroll:false,
				start:function (){
					var clase=$(this).parent().parent().find('.clase').text()
					$('.equipo .equipo_'+clase).css('background-color','#eee5b0')
					$('.equipo .equipo_'+clase).css('border-color','#ed0')
					$('.equipo .equipo_'+clase).css('border-width','4px')
					$('.equipo .equipo_'+clase).css('width','56px')
					$('.equipo .equipo_'+clase).css('height','41px')
				},
				stop:function (){
					var clase=$(this).parent().parent().find('.clase').text()
					$('.equipo .equipo_'+clase).css('background-color','')
					$('.equipo .equipo_'+clase).css('border-color','')
					$('.equipo .equipo_'+clase).css('border-width','')
					$('.equipo .equipo_'+clase).css('width','')
					$('.equipo .equipo_'+clase).css('height','')
				}
        });
		
        $("li span").droppable({
			greedy: true,
			tolerance:'pointer',
			hoverClass:'drop',
			drop: function(event,ui) {
				$(this).parent().before(ui.draggable.parent());
				
				if($(this).attr('id').substr(0,4)=='hijo')
					$.post(base_url()+'apartados/mueve/'+ui.draggable.attr('id').substr(8)+'/'+$(this).attr('id').substr(4)+'/1')
				else $.post(base_url()+'apartados/mueve/'+ui.draggable.attr('id').substr(8)+'/'+$(this).attr('id').substr(8)+'/')
			}
        });
});
