export type HeroSection = {
    type: 'hero';
    data: { heading: string; text: string };
};

export type RichTextSection = {
    type: 'rich_text';
    data: { body: string };
};

export type Section = HeroSection | RichTextSection;

export type CmsPage = {
    id: number;
    title: string;
    slug: string;
    status: 'draft' | 'published';
    sections: Section[];
};
