declare module "app/Form"
{
    const form: {
        initDataPicker(form:HTMLElement|string): void;
        initRadioAndCheckbox(form:HTMLElement|string): void;
        initSelect(form:HTMLElement|string): void;
        destroyWysiwyg(content:JQuery): void;
        initWysiwyg(form:HTMLElement|string): void;
        initSorting(form:HTMLElement|string): void;
        initInput(form:HTMLElement|string): void;
        initList(form:HTMLElement|string): void;
        markInvalidFormElements(form:HTMLElement|string, errors:Array<string>): void;
        reindex(form:JQuery|string, initialize:boolean): void;
        reindex(): void;
        initReindexableItem(item:HTMLElement|string, placeholder:string): void;
    };
    export = form;
}