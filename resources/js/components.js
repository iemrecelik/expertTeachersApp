import mainComponent from './components/admin/main/MainComponent.vue';
import searchComponent from './components/admin/old_regulation/search/SearchComponent.vue';
// import teacherInfosComponent from './components/admin/search/teacher_infos/TeacherInfosComponent.vue';
import teacherInfosComponent from './components/admin/teachers/teacher_infos/TeacherInfosComponent.vue';


/* Document Management*/
import docMngCategoryComponent from './components/admin/document_mng/category/IndexComponent.vue';
import docMngListComponent from './components/admin/document_mng/list/IndexComponent.vue';
import docMngCommentComponent from './components/admin/document_mng/comment/IndexComponent.vue';
import docMngDocumentComponent from './components/admin/document_mng/document/IndexComponent.vue';
import docMngCreateDocumentComponent from './components/admin/document_mng/document/CreateComponent.vue';
import docMngManualCreateDocumentComponent from './components/admin/document_mng/document/ManualCreateComponent.vue';
import docMngSearchComponent from './components/admin/document_mng/search/SearchComponent.vue';

/* Lawsuit Management*/
import lawsuitMngLawsuitsComponent from './components/admin/lawsuit_mng/lawsuits/IndexComponent';
import lawsuitMngStatisticalComponent from './components/admin/lawsuit_mng/statistical/IndexComponent';

/* Teacehrs */
import teachersComponent from './components/admin/teachers/IndexComponent';
import teachersPreviewComponent from './components/admin/teachers/TeachersPreviewComponent';
import teachMngInstitutionsComponent from './components/admin/institutions/IndexComponent';

/* Unions */
import unionsMngComponent from './components/admin/unions/IndexComponent';

export default {
	'main-component': mainComponent,
	'search-component': searchComponent,
	'teacher-infos-component': teacherInfosComponent,

	'doc-mng-category-component': docMngCategoryComponent,
	'doc-mng-list-component': docMngListComponent,
	'doc-mng-comment-component': docMngCommentComponent,
	'doc-mng-document-component': docMngDocumentComponent,
	'doc-mng-create-document-component': docMngCreateDocumentComponent,
	'doc-mng-manual-create-document-component': docMngManualCreateDocumentComponent,
	'doc-mng-search-component': docMngSearchComponent,

	'teachers-component': teachersComponent,
	'teachers-preview-component': teachersPreviewComponent,
	'teach-mng-institutions-component': teachMngInstitutionsComponent,

	'unions-mng-component': unionsMngComponent,

	'lawsuit-mng-lawsuits-component': lawsuitMngLawsuitsComponent,
	'lawsuit-mng-statistical-component': lawsuitMngStatisticalComponent,
}
