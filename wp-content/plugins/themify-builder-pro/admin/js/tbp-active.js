jQuery(function ($) {
    'use strict';
    var api = tb_app,
        RunEditAAP = function(el,model){
            var wrap = el.getElementsByClassName('tbp_advanchd_archive_wrap')[0],
                cl = model.get('mod_name').indexOf('product')!==-1?'_product':'',
                arhiveCL='tbp_edit'+cl+'_archive',
                singleCL='tbp_edit'+cl+'_single',
                items = $('.'+arhiveCL),
                elId=model.get('element_id'),
                data =model.get('mod_settings')['builder_content'];
            wrap.classList.add('themify_builder_content');
            wrap.classList.add('themify_builder_content-'+elId);
            wrap.classList.add('themify_builder');
            wrap.setAttribute('id','themify_builder_content-'+elId);
            wrap.setAttribute('data-postid',elId);
            api.Forms.LayoutPart.cache[elId]=JSON.parse(data);
            api.activeModel =api.ActionBar.hoverCid=data=null;
            document.body.className+=' tbp_app_is_edit';
            window.top.document.body.className+=' tbp_app_is_edit';
            items = items.add($('.'+arhiveCL,window.top.document));
            for(var i=items.length-1;i>-1;--i){
                items[i].classList.remove(arhiveCL);
                items[i].classList.add(singleCL);
            }
            $(el).one('tb_layout_part_before_init',function(){
                var saveBtn=$(this).find('.tb_toolbar_save'),
                    closeBtn=$(this).find('.tb_toolbar_close_btn');
                saveBtn.on('click',function(e){
                    e.preventDefault();
                    e.stopImmediatePropagation();
                    var data =model.get('mod_settings');
                    data['builder_content']=JSON.stringify(api.Utils.clear(api.Mixins.Builder.toJSON(api.Instances.Builder[api.builderIndex].el)));
                    model.set(data, {silent: true});
                    ThemifyBuilderCommon.showLoader('show');
                    setTimeout(function () {
                        ThemifyBuilderCommon.showLoader('hide');
                        api.Forms.LayoutPart.options=null;
                        api.Forms.LayoutPart.isSaved=true;
                    }, 100);
                });
                closeBtn.on('click',function(e){
                    e.preventDefault();
                    e.stopImmediatePropagation();
                    var isSaved=api.Forms.LayoutPart.isSaved===true;
                    api.Forms.LayoutPart.close(e);
                    if(isSaved===true || api.builderIndex===0){
                        $(this).off('click');
                        saveBtn.off('click');
                        delete api.Forms.LayoutPart.cache[elId];
                        api.activeModel =api.ActionBar.hoverCid=null;
                        document.body.classList.remove('tbp_app_is_edit');
                        window.top.document.body.classList.remove('tbp_app_is_edit');
                        for(var i=items.length-1;i>-1;--i){
                            items[i].classList.add(arhiveCL);
                            items[i].classList.remove(singleCL);
                        }
                        if(isSaved===true){
                            ThemifyBuilderCommon.showLoader('show');
                            model.trigger('custom:preview:refresh', model.get('mod_settings'));
                            setTimeout(function () {
                                api.ActionBar.hoverCid=null;
                                ThemifyBuilderCommon.showLoader('hide');
                            }, 220);
                        }
                    }
                });
                $('.tb_overlay').first().one('dblclick',function(e){
                    e.preventDefault();
                    e.stopImmediatePropagation();
                    api.Forms.LayoutPart.options=null;
                    api.Forms.LayoutPart.isSaved=true;
                    saveBtn.triggerHandler('click');
                    closeBtn.triggerHandler('click');
                });
            });
    };
    api.Constructor['tbp_advanched_layout'] = {
        render:function(data, self) {
            var f = document.createDocumentFragment();
            if(api.mode==='visual'){
                var a = document.createElement('a'),
                    run = function(e){
                        e.stopPropagation();
                        e.preventDefault();
                        this.removeEventListener('click',run,{once:true});
                        var cid = api.activeModel.cid;
                        ThemifyConstructor.saveComponent();
                        this.className+=' tb_edit';
                        api.Models.Registry.lookup(cid).trigger('edit',e,this);
                    };
                a.className='tbp_advanched_archive_edit';
                a.href='#';
                a.textContent=tbp_local.edit;
                a.addEventListener('click',run,{once:true});
                f.appendChild(a);
            }
            f.appendChild(self.hidden.render(data,self));
            return f;
        }
    };
    api.Constructor['fallback'] = {
        render:function(data, self) {
            var opt = [
                {
                    'id'      : 'fallback_s',
                    'type'    : 'toggle_switch',
                    'label' : 'fall_b',
                    'options'   : {
                            'on'  : { 'name' : 'yes', 'value' : 'en' },
                            'off' : { 'name' : 'no', 'value' : 'dis' }
                        },
                    'binding' : {
                            'checked' : {
                                    'show' : ['fallback_i' ]
                            },
                            'not_checked' : {
                                    'hide' : [ 'fallback_i' ]
                                }
                            }
                },
                {
                    'id' : 'fallback_i',
                    'type' : 'image',
                    'wrap_class' : 'pushed',
                    'class' : 'xlarge'
                }];
           return self.create(opt);
        }
    };
    api.Constructor['tbp_custom_css'] = {
        render:function(data, self) {
            var opt = [
                {
                    'id'      : 'css',
                    'type'    : 'custom_css'
                },
                {
                    'type' : 'custom_css_id'
                }];
           return self.create(opt);
        }
    };
    if(api.mode==='visual'){
        if(tbp_local['id']!==undefined){
            $.ajaxPrefilter(function( options, originalOptions, jqXHR ) {
               if(originalOptions['data']['action']==='tb_render_element' || originalOptions['data']['action']==='tb_load_module_partial'){
                   options['data']+='&pageId='+tbp_local['id']+'&type='+tbp_local['type'];
               }
            });
        }
        Themify.body.on('tb_edit_advanced-posts tb_edit_advanced-products',function(e,ev,el,model){
            if(!api.Forms.LayoutPart.id && ev && ev.target.classList.contains('tb_edit')){
                RunEditAAP(el,model);
                return true;
            }
        });
        Themify.LoadCss(tbp_local.cssUrl, tbp_local.v);
        window.top.Themify.LoadCss(tbp_local.cssUrl, tbp_local.v);
        tbp_local.cssUrl=null;
        
    }
});