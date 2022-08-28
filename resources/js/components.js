import mainComponent from './components/admin/main/MainComponent.vue';
import searchComponent from './components/admin/old_regulation/search/SearchComponent.vue';
import teacherInfosComponent from './components/admin/search/teacher_infos/TeacherInfosComponent.vue';

/* Document Management Category */
import docMngCategoryComponent from './components/admin/document_mng/category/IndexComponent.vue';
import docMngDocumentComponent from './components/admin/document_mng/document/IndexComponent.vue';
import docMngCreateDocumentComponent from './components/admin/document_mng/document/CreateComponent.vue';
import docMngSearchComponent from './components/admin/document_mng/search/SearchComponent.vue';


export default {
	'main-component': mainComponent,
	'search-component': searchComponent,
	'teacher-infos-component': teacherInfosComponent,

	'doc-mng-category-component': docMngCategoryComponent,
	'doc-mng-document-component': docMngDocumentComponent,
	'doc-mng-create-document-component': docMngCreateDocumentComponent,
	'doc-mng-search-component': docMngSearchComponent,
}
